<?php
final class Application
{
    private static $_instance = null;
    private $_isViewRender = true;
    private $_configInstance = null;
    private $_requestInstance = null;
    private $_responseInstance = null;
    private $_cacheInstanceArray = [];
    private $_databaseInstanceArray = [];
    private $_modelInstanceArray = [];
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    public function __construct()
    {
        $this->_configInstance = new Config();
        $this->_requestInstance = new Request();
        $this->_responseInstance = new Response();
    }
    public function disableView()
    {
        $this->_isViewRender = false;
        return $this;
    }
    public function run()
    {
        if (SESSION_CACHE_ENABLE) {
            ini_set('session.gc_probability', 0);
            session_set_save_handler(new Session(), true);
        } else {
            ini_set('session.gc_probability', 1);
        }
        ini_set('session.auto_start', 0);
        session_start();
        $routerInstance = new Router();
        $routerInstance->route();
        unset($routerInstance);
        $controller = ucfirst($this->_requestInstance->getControllerName()) . 'Controller';
        $controllerFile = ROOT . '/application/module/' . MODULE . '/controller/' . $controller . '.php';
        if (!is_file($controllerFile)) {
            throw new FileNotFoundException($controllerFile, '控制器文件丢失');
        }
        require $controllerFile;
        $action = $this->_requestInstance->getActionName() . 'Action';
        $controllerInstance = new $controller();
        if (!method_exists($controllerInstance, $action)) {
            throw new UndefinedException('function', $action, '控制器 ' . $controller . ' 下未定义动作');
        }
        $ret = $controllerInstance->$action();
        unset($controllerInstance);
        if ($this->_isViewRender) {
            $viewInstance = new View();
            $ret = $viewInstance->render($this->_requestInstance->getActionName() . '.php', $ret);
            unset($viewInstance);
        }
        $this->_responseInstance->setBody($ret);
        $this->_responseInstance->output();
    }
    public function getConfigInstance()
    {
        return $this->_configInstance;
    }
    public function getRequestInstance()
    {
        return $this->_requestInstance;
    }
    public function getResponseInstance()
    {
        return $this->_responseInstance;
    }
    public function getCacheInstance($driverName = null)
    {
        $driverName = $driverName === null ? ucfirst(Application::getInstance()->getConfigInstance()->get('cache.driver')) : $driverName;
        if (!isset($this->_cacheInstanceArray[$driverName])) {
            $this->_cacheInstanceArray[$driverName] = new $driverName();
        }
        return $this->_cacheInstanceArray[$driverName];
    }
    public function getDatabaseInstance($driverName = null)
    {
        $driverName = $driverName === null ? ucfirst(Application::getInstance()->getConfigInstance()->get('database.driver')) : $driverName;
        if (!isset($this->_databaseInstanceArray[$driverName])) {
            $this->_databaseInstanceArray[$driverName] = new $driverName();
        }
        return $this->_databaseInstanceArray[$driverName];
    }
    public function getModelInstance($modelName)
    {
        $modelName = ucfirst($modelName) . 'Model';
        if (!isset($this->_modelInstanceArray[$modelName])) {
            $modelFile = ROOT . '/application/model/' . $modelName . '.php';
            if (!is_file($modelFile)) {
                throw new FileNotFoundException($modelFile, '模型文件丢失');
            }
            require $modelFile;
            if (!class_exists($modelName, false)) {
                throw new UndefinedException('class', $modelName, '模型类未定义');
            }
            $this->_modelInstanceArray[$modelName] = new $modelName();
        }
        return $this->_modelInstanceArray[$modelName];
    }
}
abstract class ControllerAbstract
{
    public function json($data)
    {
        // 关闭视图
        Application::getInstance()->disableView();
        // 设置 json 响应头
        Application::getInstance()->getResponseInstance()->setHeader('Content-Type', 'application/json;charset=UTF-8', true, 200);
        // json 格式（成功状态）
        return json_encode([
            'status' => true,
            'code' => '',
            'data' => $data
        ]);
    }
    public function image($data)
    {
        // 关闭视图
        Application::getInstance()->disableView();
        // 设置 image 响应头
        Application::getInstance()->getResponseInstance()->setHeader('Content-Type', 'image/jpeg', true, 200);
        return $data;
    }
    public function redirect($url)
    {
        // 关闭视图
        Application::getInstance()->disableView();
        // 设置重定向响应头
        Application::getInstance()->getResponseInstance()->setHeader('Location', $url);
        return true;
    }
}
abstract class ModelAbstract
{
    protected $_databaseInstance = null;
    public function __construct()
    {
        $this->_databaseInstance = Application::getInstance()->getDatabaseInstance();
    }
}
final class Config
{
    private $_configArray = null;
    public function __construct($configArray = null)
    {
        if ($configArray !== null) {
            $this->_configArray = $configArray;
        } else {
            // 系统配置
            $systemConfigFile = ROOT . '/application/config/' . ENV . '.php';
            if (!is_file($systemConfigFile)) {
                throw new FileNotFoundException($systemConfigFile, '系统配置文件丢失');
            }
            $config = include $systemConfigFile;
            // 应用配置
            $moduleConfigFile = ROOT . '/application/module/' . MODULE . '/config/' . ENV . '.php';
            if (is_file($moduleConfigFile)) {
                $config = array_merge(
                    $config,
                    include $moduleConfigFile
                );
            }
            $this->_configArray = $config;
        }
    }
    public function get($key)
    {
        $ret = null;
        if (strpos($key, '.') > 0) {
            $array = explode('.', $key);
            switch (count($array)) {
                case 2:
                    $ret = isset($this->_configArray[$array[0]][$array[1]]) ? $this->_configArray[$array[0]][$array[1]] : null;
                    break;
                case 3:
                    $ret = isset($this->_configArray[$array[0]][$array[1]][$array[2]]) ? $this->_configArray[$array[0]][$array[1]][$array[2]] : null;
                    break;
                default:
                    break;
            }
        } else {
            $ret = isset($this->_configArray[$key]) ? $this->_configArray[$key] : null;
        }
        if (is_null($ret)) {
            throw new UndefinedException('config', $key, '配置不存在');
        }
        return $ret;
    }
}
final class Request
{
    private $_method = null;
    private $_controllerName = null;
    private $_actionName = null;
    public function getGlobalVariable($source, $key = null, $default = null, $filter = null)
    {
        if (!in_array(strtolower($source), ['get', 'post', 'request', 'server', 'files', 'env', 'cookie', 'session'])) {
            return null;
        }
        $data = null;
        eval('$data = $_' . strtoupper($source) . ';');
        if ($key === null) {
            return $data;
        }
        if (!isset($data[$key])) {
            return $default;
        }
        $value = $data[$key];
        if ($filter !== null) {
            if (strpos($filter, '/') === 0) {
                $value = preg_match($filter, $value) ? $value : $default;
            } else {
                $value = call_user_func($filter, $value);
            }
        }
        return $value;
    }
    public function getMethod()
    {
        if ($this->_method === null) {
            $method = $this->getGlobalVariable('server', 'REQUEST_METHOD');
            if ($method) {
                $this->_method = strtoupper($method);
            } else {
                $sapi = php_sapi_name();
                if (strtolower($sapi) === 'cli' || strtolower(substr($sapi, 0, 3)) === 'cgi') {
                    $this->_method = 'CLI';
                } else {
                    $this->_method = 'UNKNOWN';
                }
            }
        }
        return $this->_method;
    }
    public function isCli()
    {
        return $this->getMethod() === 'CLI';
    }
    public function setControllerName($controllerName)
    {
        $this->_controllerName = $controllerName;
        return $this;
    }
    public function setActionName($actionName)
    {
        $this->_actionName = $actionName;
        return $this;
    }
    public function getControllerName()
    {
        return $this->_controllerName;
    }
    public function getActionName()
    {
        return $this->_actionName;
    }
}
final class Response
{
    private $_headerArray = [];
    private $_body = null;
    public function __construct()
    {
        // 正常渲染视图的请求会使用这个默认的响应头
        // ControllerAbstract 中的 json() / image() 会使用自己覆盖后的响应头，redirect() 的 Location 在这之后，所以不会存在跳转不了的问题
        $this->setHeader('Content-Type', 'text/html;charset=UTF-8', true, 200);
    }
    public function setHeader($name, $value, $replace = true, $code = 0)
    {
        if ($replace === true) {
            foreach ($this->_headerArray as $k => $v) {
                if ($v['name'] === $name) {
                    unset($this->_headerArray[$k]);
                }
            }
        }
        $this->_headerArray[] = [
            'name' => $name,
            'value' => $value,
            'replace' => $replace,
            'code' => $code
        ];
    }
    public function setBody($content)
    {
        $this->_body = $content;
    }
    public function sendHeaders()
    {
        foreach ($this->_headerArray as $header) {
            if ($header['code'] !== 0) {
                header(
                    $header['name'] . ':' . $header['value'],
                    $header['replace'],
                    $header['code']
                );
            } else {
                header(
                    $header['name'] . ':' . $header['value'],
                    $header['replace']
                );
            }
        }
    }
    public function output()
    {
        $this->sendHeaders();
        exit($this->_body);
    }
}
final class Router
{
    const DEFAULT_CONTROLLER_NAME = 'index';
    const DEFAULT_ACTION_NAME = 'index';
    public function route()
    {
        $controllerName = Application::getInstance()->getRequestInstance()->getGlobalVariable('get', 'c', self::DEFAULT_CONTROLLER_NAME);
        $actionName = Application::getInstance()->getRequestInstance()->getGlobalVariable('get', 'a', self::DEFAULT_ACTION_NAME);
        $controllerName = preg_match('/^[a-z]+$/', $controllerName) ? $controllerName : self::DEFAULT_CONTROLLER_NAME;
        $actionName = preg_match('/^[a-zA-Z]+$/', $actionName) ? $actionName : self::DEFAULT_ACTION_NAME;
        Application::getInstance()->getRequestInstance()->setControllerName($controllerName)->setActionName($actionName);
    }
}
final class Session implements \SessionHandlerInterface
{
    private $_cacheInstance = null;
    public function __construct()
    {
        $this->_cacheInstance = Application::getInstance()->getCacheInstance();
    }
    public function open($savePath, $sessionName)
    {
        return true;
    }
    public function read($sessionId)
    {
        return $this->_cacheInstance->get('sess-' . $sessionId);
    }
    public function write($sessionId, $sessionData)
    {
        $this->_cacheInstance->set('sess-' . $sessionId, $sessionData, SESSION_CACHE_TIMEOUT);
    }
    public function close()
    {
        return true;
    }
    public function destroy($sessionId)
    {
        $this->_cacheInstance->delete('sess-' . $sessionId);
    }
    public function gc($maxLifetime)
    {
        return false;
    }
}
final class View
{
    private $_viewPath = null;
    public function __construct()
    {
        // 暂时默认视图到这里（目前不提供 setViewPath()，也不支持第三方如 smarty / volt 等模版引擎，将来可扩展）
        $this->_viewPath = ROOT . '/application/module/' . MODULE . '/view/' . Application::getInstance()->getRequestInstance()->getControllerName();
    }
    public function render($viewFileName, $data)
    {
        extract($data);
        ob_start();
        $viewFile = $this->_viewPath . '/' . $viewFileName;
        if (!is_file($viewFile)) {
            throw new FileNotFoundException($viewFile, '视图文件丢失');
        }
        include($viewFile);
        $ret = ob_get_clean();
        return $ret;
    }
}
interface CacheInterface
{
    public function get($key);
    public function set($key, $value, $expiration);
}
final class MemcacheX implements CacheInterface
{
    private $_memcacheInstance = null;
    public function __construct()
    {
        try {
            $memcached = new \Memcache();
            $servers = Application::getInstance()->getConfigInstance()->get('cache.servers');
            foreach ($servers as $server) {
                $memcached->addServer($server['HOST'], $server['PORT']);
            }
            $this->_memcacheInstance = $memcached;
        } catch (StorageException $e) {
            throw new StorageException('memcached', $e->getMessage(), $e->getCode());
        }
    }
    public function get($key)
    {
        return $this->_memcacheInstance->get($key);
    }
    public function set($key, $value, $expiration = 0)
    {
        return $this->_memcacheInstance->set($key, $value, MEMCACHE_COMPRESSED, $expiration);
    }
    public function delete($key, $timeout = 0)
    {
        return $this->_memcacheInstance->delete($key, $timeout);
    }
}
interface DatabaseInterface
{
    public function getConnect($isMaster);
    public function query($sql);
    public function execute($sql);
    public function field();
    public function table($tableName);
    public function join($way, $tableName, $leftField, $rightField);
    public function where();
    public function order();
    // public function group();
    // public function having();
    public function limit();
    public function select();
    public function insert($data);
    public function update($data);
    public function delete();
    public function startTrans();
    public function rollback();
    public function commit();
}
final class MysqliX implements DatabaseInterface
{
    private $_connectArray = [];
    private $_data = [];
    public function getConnect($isMaster)
    {
        $database = Application::getInstance()->getConfigInstance()->get('database');
        // 选取配置
        $config = $isMaster ? $database['master'] : $database['slaves'][mt_rand(0, count($database['slaves']) - 1)];
        // 一种配置对应一个连接
        $key = md5(implode('', $config));
        // connect
        if (!isset($this->_connectArray[$key])) {
            $connect = new \Mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);
            $connect->query('SET NAMES ' . $config['charset']);
            if (mysqli_connect_errno()) {
                throw new StorageException('mysqli', 'getConnect: ' . mysqli_connect_error());
            }
            $this->_connectArray[$key] = $connect;
        }
        return $this->_connectArray[$key];
    }
    public function query($sql)
    {
        $query = $this->getConnect(false)->query($sql);
        if ($query === false) {
            // todo: log
            return false;
        }
        $result = [];
        if ($query->num_rows > 0) {
            for ($i = 0; $i < $query->num_rows; $i++) {
                $result[] = $query->fetch_assoc();
            }
        }
        return $result;
    }
    public function execute($sql)
    {
        $connect = $this->getConnect(true);
        $query = $connect->query($sql);
        if ($query === false) {
            // todo: log
            return false;
        }
        if (strpos($sql, 'INSERT') === 0) {
            return $connect->insert_id;
        } elseif(strpos($sql, 'UPDATE') === 0) {
            return $connect->affected_rows > 0;
        } else {
            return true;
        }
    }
    public function field()
    {
        $args = func_get_args();
        $argNum = func_num_args();
        $field = null;
        if ($argNum === 0) {
            $field = '*';
        } else {
            $array = [];
            foreach ($args as $arg) {
                if (is_string($arg)) {
                    $array[] = $arg;
                } elseif (is_array($arg)) {
                    $array[] = array_keys($arg)[0] . ' AS ' . $arg[array_keys($arg)[0]];
                }
            }
            $field = implode(', ', $array);
        }
        $this->_data['field'] = $field;
        return $this;
    }
    public function table($tableName)
    {
        $table = null;
        if (is_string($tableName)) {
            $table = $tableName;
        } elseif (is_array($tableName)) {
            $table = array_keys($tableName)[0] . ' AS ' . $tableName[array_keys($tableName)[0]];
        }
        $this->_data['table'] = $table;
        return $this;
    }
    public function join($way, $tableName, $leftField, $rightField)
    {
        $table = null;
        if (is_string($tableName)) {
            $table = $tableName;
        } elseif (is_array($tableName)) {
            $table = array_keys($tableName)[0] . ' AS ' . $tableName[array_keys($tableName)[0]];
        }
        $join = ' ' . strtoupper($way) . ' JOIN ' . $table . ' ON ' . $leftField . ' = ' . $rightField;
        if (isset($this->_data['join'])) {
            $this->_data['join'] .= $join;
        } else {
            $this->_data['join'] = $join;
        }
        return $this;
    }
    public function where()
    {
        $args = func_get_args();
        $argNum = func_num_args();
        $where = null;
        if ($argNum > 0) {
            if ($argNum === 1) {
                $linkWord = array_keys($args[0])[0];
                $array = [];
                foreach ($args[0][$linkWord] as $k => $v) {
                    if (is_string($k)) {
                        $arrayInner = array();
                        foreach ($v as $item) {
                            $arrayInner[] = '(' . $this->_parseWhere($item[0], $item[1], $item[2]) . ')';
                        }
                        $array[] = '(' . implode(' ' . strtoupper($k) . ' ', $arrayInner) . ')';
                    } else {
                        $array[] = '(' . $this->_parseWhere($v[0], $v[1], $v[2]) . ')';
                    }
                }
                $where = implode(' ' . strtoupper($linkWord) . ' ', $array);
            } elseif ($argNum === 3) {
                $where = $this->_parseWhere($args[0], $args[1], $args[2]);
            }
        }
        $this->_data['where'] = ' WHERE ' . $where;
        return $this;
    }
    private function _parseWhere($field, $condition, $value)
    {
        $condition = str_replace(
            ['eq', 'neq', 'lk', 'nlk', 'bt', 'nbt', 'in', 'nin'],
            ['=', '<>', 'LIKE', 'NOT LIKE', 'BETWEEN', 'NOT BETWEEN', 'IN', 'NOT IN'],
            $condition
        );
        if (is_array($value)) {
            $value = '(' . implode(',', $value) . ')';
        } elseif (is_string($value)) {
            $value = '"' . $value . '"';
        }
        return $field . ' ' . $condition . ' ' . $value;
    }
    public function order()
    {
        $args = func_get_args();
        $argNum = func_num_args();
        $order = null;
        if ($argNum > 0) {
            if ($argNum === 1) {
                $order = $args[0] . ' ASC';
            } else {
                if ($argNum === 2 && is_string($args[0]) && is_string($args[1])) {
                    $order = $args[0] . ' ' . strtoupper($args[1]);
                } else {
                    $array = [];
                    foreach ($args as $arg) {
                        $array[] = $arg[0] . ' ' . strtoupper($arg[1]);
                    }
                    $order = implode(', ', $array);
                }
            }
        }
        $this->_data['order'] = ' ORDER BY ' . $order;
        return $this;
    }
    public function limit()
    {
        $args = func_get_args();
        $argNum = func_num_args();
        $limit = null;
        if ($argNum > 0) {
            if ($argNum === 1) {
                $limit = $args[0];
            } elseif ($argNum === 2) {
                $limit = $args[0] . ', ' . $args[1];
            }
        }
        $this->_data['limit'] = ' LIMIT ' . $limit;
        return $this;
    }
    public function select()
    {
        $field = isset($this->_data['field']) ? $this->_data['field'] : '*';
        if (!isset($this->_data['table'])) {
            throw new StorageException('mysqli', 'select: 缺少表名');
        }
        $join = isset($this->_data['join']) ? $this->_data['join'] : '';
        $where = isset($this->_data['where']) ? $this->_data['where'] : '';
        $order = isset($this->_data['order']) ? $this->_data['order'] : '';
        $limit = isset($this->_data['limit']) ? $this->_data['limit'] : '';
        // mysql ELECT
        $sql = 'SELECT ' . $field . ' FROM ' . $this->_data['table'] . $join . $where . $order . $limit;
        return $this->query($sql);
    }
    public function insert($data)
    {
        if (!isset($this->_data['table'])) {
            throw new StorageException('mysqli', 'inser: 缺少表名');
        }
        $keys = implode(', ', array_keys($data));
        foreach ($data as $k => $v) {
            $data[$k] = is_string($v) ? '"' . $v . '"' : $v;
        }
        $value = implode(', ', $data);
        // mysql INSERT
        $sql = 'INSERT INTO ' . $this->_data['table'] . '(' . $keys . ') VALUES (' . $value . ')';
        return $this->execute($sql);
    }
    public function update($data)
    {
        if (!isset($this->_data['table'])) {
            throw new StorageException('mysqli', 'inser: 缺少表名');
        }
        $where = isset($this->_data['where']) ? $this->_data['where'] : '';
        $array = [];
        foreach ($data as $k => $v) {
            if (strpos($v, '+') === 0 || strpos($v, '-') === 0) {
                $array[] = $k . ' = ' . $k . ' ' . $v;
            } else {
                $array[] = is_string($v) ? ($k . ' = "' . $v . '"') : ($k . ' = ' . $v);
            }
        }
        $set = implode(', ', $array);
        // mysql UPDATE
        $sql = 'UPDATE ' . $this->_data['table'] . ' SET ' . $set . $where;
        return $this->execute($sql);
    }
    public function delete()
    {}
    public function startTrans()
    {
        $connect = $this->getConnect(true);
        $connect->autocommit(false);
        return $connect->begin_transaction();
    }
    public function rollback()
    {
        $connect = $this->getConnect(true);
        $rollback = $connect->rollback();
        $connect->autocommit(true);
        return $rollback;
    }
    public function commit()
    {
        $connect = $this->getConnect(true);
        $commit = $connect->commit();
        $connect->autocommit(true);
        return $commit;
    }
}
class StorageException extends \Exception
{
    private $_name = null;
    public function __construct($name, $message, $code = 0)
    {
        $this->_name = $name;
        parent::__construct($message, $code);
    }
    public function getName()
    {
        return $this->_name;
    }
}
class MailerException extends \Exception
{
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }
}
class UndefinedException extends \Exception
{
    private $_type = null;
    private $_name = null;
    public function __construct($type, $name, $message, $code = 0)
    {
        $this->_type = $type;
        $this->_name = $name;
        parent::__construct($message, $code);
    }
    public function getType()
    {
        return $this->_type;
    }
    public function getName()
    {
        return $this->_name;
    }
}
class FileNotFoundException extends \Exception
{
    protected $_filePath = null;
    public function __construct($filePath, $message, $code = 0)
    {
        $this->_filePath = $filePath;
        parent::__construct($message, $code);
    }
    public function getFilePath()
    {
        return $this->_filePath;
    }
}
class HttpException extends \Exception
{
    private $_statusCode = null;
    public function __construct($statusCode, $message, $code = 0)
    {
        $this->_statusCode = $statusCode;
        parent::__construct($message, $code);
    }
    public function getStatusCode()
    {
        return $this->_statusCode;
    }
}
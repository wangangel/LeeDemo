<?php
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
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 10010);
        }
        $servers = Application::getInstance()->getConfigInstance()->get('cache.servers');
        foreach ($servers as $server) {
            $addServer = $memcached->addServer($server['HOST'], $server['PORT']);
            if (!$addServer) {
                // todo: memcache 服务未启动，addServer 居然还是返回 true
                throw new \Exception('MemcacheX', 10011);
            }
        }
        $this->_memcacheInstance = $memcached;
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
function vendor($fileName)
{
    static $_vendorArray = [];
    if (!isset($_vendorArray[$fileName])) {
        $file = ROOT . '/library/vendor/' . $fileName;
        if (!is_file($file)) {
            throw new \Exception($file, 10016);
        } else {
            require $file;
        }
    }
    return ($_vendorArray[$fileName] = true);
}
function mailer($address, $subject, $body)
{
    vendor('PHPMailer-master/PHPMailerAutoload.php');
    $mail = new \PHPMailer();
    $mailConfig = Application::getInstance()->getConfigInstance()->get('mail');
    // $mail->SMTPDebug = 3; // Enable verbose debug output
    $mail->isSMTP();
    $mail->Host = $mailConfig['host'];
    $mail->SMTPAuth = true;
    $mail->Username = $mailConfig['username'];
    $mail->Password = $mailConfig['password'];
    $mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
    $mail->Port = $mailConfig['port'];
    $mail->setFrom($mailConfig['fromAddress'], $mailConfig['fromName']);
    $mail->addAddress($address);
    // $mail->addReplyTo('info@example.com', 'Information');
    // $mail->addCC('cc@example.com');
    // $mail->addBCC('bcc@example.com');
    // $mail->addAttachment('/var/tmp/file.tar.gz');
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    if(!$mail->send()) {
        throw new \Exception($mail->ErrorInfo, 10017);
    }
    return true;
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
                throw new \Exception(mysqli_connect_error(), 10012);
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
            throw new \Exception('MysqliX', 10013);
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
            throw new \Exception('MysqliX', 10014);
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
            throw new \Exception('MysqliX', 10015);
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
final class Application
{
    private static $_instance = null;
    private $_isViewRender = true;
    private $_configInstance = null;
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
    }
    public function disableView()
    {
        $this->_isViewRender = false;
        return $this;
    }
    public function bootstrap()
    {
        $bootstrapFile = ROOT . '/application/module/' . MODULE . '/Bootstrap.php';
        if (!is_file($bootstrapFile)) {
            throw new \Exception($bootstrapFile, 10000);
        }
        require $bootstrapFile;
        if (!class_exists('Bootstrap', false)) {
            throw new \Exception('Bootstrap', 10001);
        }
        $bootstrapInstance = new Bootstrap();
        $functionArray = get_class_methods($bootstrapInstance);
        foreach ($functionArray as $function) {
            if (substr($function, 0, 5) === '_init') {
                call_user_func([$bootstrapInstance, $function]);
            }
        }
        return $this;
    }
    public function run()
    {
        $this->_configInstance->load(ROOT . '/application/module/' . MODULE . '/config/' . ENV . '.php');
        if (SESSION_CACHE_ENABLE) {
            ini_set('session.gc_probability', 0);
            session_set_save_handler(new Session(), true);
        } else {
            ini_set('session.gc_probability', 1);
        }
        ini_set('session.auto_start', 0);
        session_start();
        $requestInstance = new Request();
        $routerInstance = new Router();
        $routerInstance->route($requestInstance);
        unset($routerInstance);
        $controller = ucfirst($requestInstance->getControllerName()) . 'Controller';
        $controllerFile = ROOT . '/application/module/' . MODULE . '/controller/' . $controller . '.php';
        if (!is_file($controllerFile)) {
            throw new \Exception($controllerFile, 10002);
        }
        require $controllerFile;
        if (!class_exists($controller, false)) {
            throw new \Exception($controller, 10003);
        }
        $controllerInstance = new $controller();
        $action = $requestInstance->getActionName() . 'Action';
        if (!method_exists($controllerInstance, $action)) {
            throw new \Exception($action, 10004);
        }
        $ret = call_user_func([$controllerInstance, $action]);
        unset($controllerInstance);
        if ($this->_isViewRender) {
            $viewInstance = new View();
            $ret = $viewInstance
                ->setViewPath(ROOT . '/application/module/' . MODULE . '/view/' . $requestInstance->getControllerName())
                ->render($requestInstance->getActionName() . '.php', $ret);
            unset($viewInstance);
        }
        unset($requestInstance);
        $responseInstance = new Response();
        $responseInstance->setBody($ret)->output();
    }
    public function getConfigInstance()
    {
        return $this->_configInstance;
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
                throw new \Exception($modelFile, 10005);
            }
            require $modelFile;
            if (!class_exists($modelName, false)) {
                throw new \Exception($modelName, 10006);
            }
            $this->_modelInstanceArray[$modelName] = new $modelName();
        }
        return $this->_modelInstanceArray[$modelName];
    }
}
final class Config
{
    private $_configArray = null;
    public function load($source)
    {
        $config = null;
        if (is_string($source)) {
            if (!is_file($source)) {
                throw new \Exception($source, 10007);
            }
            $config = include $source;
        } elseif (is_array($source)) {
            $config = $source;
        } else {
            throw new \Exception($source, 10018);
        }
        if ($config !== null) {
            if ($this->_configArray === null) {
                $this->_configArray = $config;
            } else {
                $this->_configArray = array_merge($this->_configArray, $config);
            }
        }
    }
    public function get($key = null)
    {
        $ret = null;
        if ($key === null) {
            $ret = $this->_configArray;
        } elseif (strpos($key, '.') > 0) {
            $array = explode('.', $key);
            switch (count($array)) {
                case 2:
                    $ret = $this->_configArray[$array[0]][$array[1]] === null ? null : $this->_configArray[$array[0]][$array[1]];
                    break;
                case 3:
                    $ret = $this->_configArray[$array[0]][$array[1]][$array[2]] === null ? null : $this->_configArray[$array[0]][$array[1]][$array[2]];
                    break;
                default:
                    break;
            }
        } else {
            $ret = $this->_configArray[$key] === null ? null : $this->_configArray[$key];
        }
        if ($ret === null) {
            throw new \Exception($key, 10008);
        }
        return $ret;
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
        return $this;
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
    public function route(Request $requestInstance)
    {
        $controllerName = $requestInstance->getGlobalVariable('get', 'c', self::DEFAULT_CONTROLLER_NAME);
        $actionName = $requestInstance->getGlobalVariable('get', 'a', self::DEFAULT_ACTION_NAME);
        $controllerName = preg_match('/^[a-z]+$/', $controllerName) ? $controllerName : self::DEFAULT_CONTROLLER_NAME;
        $actionName = preg_match('/^[a-zA-Z]+$/', $actionName) ? $actionName : self::DEFAULT_ACTION_NAME;
        $requestInstance->setControllerName($controllerName)->setActionName($actionName);
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
    public function setViewPath($viewPath)
    {
        $this->_viewPath = $viewPath;
        return $this;
    }
    public function render($viewFileName, $data)
    {
        extract($data);
        ob_start();
        $viewFile = $this->_viewPath . '/' . $viewFileName;
        if (!is_file($viewFile)) {
            throw new \Exception($viewFile, 10009);
        }
        include($viewFile);
        $ret = ob_get_clean();
        return $ret;
    }
}
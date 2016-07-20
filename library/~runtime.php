<?php
final class Application
{
    private static $_instance = null;
    private $_isViewRender = true;
    private $_configInstance = null;
    private $_requestInstance = null;
    private $_responseInstance = null;
    private $_modelInstanceArray = [];
    private $_hookInstanceArray = [];
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
    public function bootstrap()
    {
        $bootstrapFile = ROOT . '/application/module/' . MODULE . '/Bootstrap.php';
        if (!is_file($bootstrapFile)) {
            throw new FileNotFoundException($bootstrapFile, '当前应用的初始化文件丢失');
        }
        require $bootstrapFile;
        if (!class_exists('Bootstrap', false)) {
            throw new UndefinedException('class', 'Bootstrap', '当前应用的初始化类未定义');
        }
        $obj = new Bootstrap();
        $methodArr = get_class_methods($obj);
        foreach ($methodArr as $method) {
            if (substr($method, 0, 5) === '_init') {
                $obj->$method();
            }
        }
        return $this;
    }
    public function run()
    {
        // todo: beforeRoute Hook
        // 执行路由
        $routerInstance = new Router();
        $routerInstance->route();
        unset($routerInstance);
        // todo: beforeDispatch Hook
        // 执行分发
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
        // todo: beforeRender Hook
        // 是否渲染视图
        if ($this->_isViewRender) {
            $viewInstance = new View();
            $ret = $viewInstance->render($this->_requestInstance->getActionName() . '.php', $ret);
            unset($viewInstance);
        }
        $this->_responseInstance->setBody($ret);
        // todo: beforeResponse Hook
        // 执行响应
        $this->_responseInstance->response();
    }
    public function disableView()
    {
        $this->_isViewRender = false;
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
    public function registerHook($hookInstance)
    {
        $this->_hookInstanceArray[] = $hookInstance;
        return $this;
    }
}
abstract class ControllerAbstract
{
}
abstract class ModelAbstract
{
    protected $_db = null;
    public function __construct()
    {
        $this->_db = DatabaseFactory::getDriverInstance();
    }
}
interface HookInterface
{
    public function beforeRoute();
    public function beforeDispatch();
    public function beforeRender();
    public function beforeResponse();
}
class Config
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
    public function getGlobalQuery($key = null, $default = null, $filter = null)
    {
        if ($key === null) {
            return $_GET;
        }
        $ret = null;
        if(isset($_GET[$key])){
            $ret = $_GET[$key];
            if ($filter !== null) {
                if (substr($filter, 0, 1) === '/') {
                    if (!preg_match($filter, $ret)) {
                        $ret = $default;
                    }
                } else {
                    $ret = $filter($ret);
                }
            }
        } else {
            $ret = $default;
        }
        return $ret;
    }
    public function getGlobalPost($key = null, $default = null)
    {
        if ($key === null) {
            return $_POST;
        }
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }
    public function getGlobalRequest($key = null, $default = null)
    {
        if ($key === null) {
            return $_REQUEST;
        }
        return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $default;
    }
    public function getGlobalServer($key = null, $default = null)
    {
        if ($key === null) {
            return $_SERVER;
        }
        return isset($_SERVER[$key]) ? $_SERVER[$key] : $default;
    }
    public function getMethod()
    {
        if ($this->_method === null) {
            $method = $this->getGlobalServer('REQUEST_METHOD');
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
    private $_body = null;
    public function setBody($content)
    {
        $this->_body = $content;
    }
    public function getBody()
    {
        return $this->_body;
    }
    public function response()
    {
        echo $this->getBody();
    }
}
final class Router
{
    const DEFAULT_CONTROLLER_NAME = 'index';
    const DEFAULT_ACTION_NAME = 'index';
    public function route()
    {
        $requestInstance = Application::getInstance()->getRequestInstance();
        $controllerName = $requestInstance->getGlobalQuery('c', self::DEFAULT_CONTROLLER_NAME, '/^[a-z]+$/');
        $actionName = $requestInstance->getGlobalQuery('a', self::DEFAULT_ACTION_NAME, '/^[a-zA-Z]+$/');
        $requestInstance->setControllerName($controllerName)->setActionName($actionName);
    }
}
final class Session
{
    private static $_instance = null;
    public function __construct()
    {
        session_start();
        ini_set('session.save_handler', SESSION_SAVE_HANDLER);
    }
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
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
final class Mysqlii implements DatabaseInterface
{
    private $_connectArray = [];
    private $_data = [];
    public function getConnect($isMaster)
    {
        $config = C('db');
        if ($isMaster) {
            $config = $config['master'];
        } else {
            $config = $config['slave'][mt_rand(0, 1)];
        }
        // 一种配置对应一个连接
        $key = md5(implode('', $config));
        if (!isset($this->_connectArray[$key])) {
            $connect = new \Mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);
            $connect->query('SET NAMES ' . $config['charset']);
            if (mysqli_connect_errno()) {
                throw new DatabaseException('mysqli getConnect: ' . mysqli_connect_error());
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
            throw new DatabaseException('mysqli query: ' . $sql);
        }
        $result = [];
        if ($query->num_rows > 0) {
            for ($i = 0; $i < $query->num_rows; $i++) {
                $result[$i] = $query->fetch_assoc();
            }
        }
        return $result;
    }
    public function execute($sql)
    {}
    public function field()
    {
        $args = func_get_args();
        $num = func_num_args();
        $field = null;
        if ($num === 0) {
            $field = '*';
        } elseif ($num === 1) {
            if (!is_string($args[0])) {
                throw new DatabaseException('mysqli field: 无效的参数');
            }
            $field = $args[0] === '*' ? '*' : $args[0];
        } else {
            $array = [];
            foreach ($args as $arg) {
                if (is_string($arg)) {
                    $array[] = $arg;
                } elseif (is_array($arg)) {
                    $array[] = array_keys($arg)[0] . ' AS ' . $arg[array_keys($arg)[0]];
                } else {
                    throw new DatabaseException('mysqli field: 无效的参数');
                }
            }
            $field = implode(', ', $array);
        }
        $this->_data['field'] = $field;
        return $this;
    }
    public function table($tableName)
    {
        if (empty($tableName)) {
            throw new DatabaseException('mysqli table: 缺少参数 -> $tableName');
        }
        $table = null;
        if (is_string($tableName)) {
            $table = $tableName;
        } elseif (is_array($tableName)) {
            $table = array_keys($tableName)[0] . ' AS ' . $tableName[array_keys($tableName)[0]];
        } else {
            throw new DatabaseException('mysqli table: 无效的参数 -> $tableName');
        }
        $this->_data['table'] = $table;
        return $this;
    }
    public function join($way, $tableName, $leftField, $rightField)
    {
        $allowWays = ['inner', 'left', 'left outer', 'right', 'right outer', 'full', 'full outer'];
        if (!in_array(strtolower($way), $allowWays)) {
            throw new DatabaseException('mysqli join: 无效的参数 -> $way');
        }
        $table = null;
        if (empty($tableName)) {
            throw new DatabaseException('mysqli join: 缺少参数 -> $tableName');
        } else {
            if (is_string($tableName)) {
                $table = $tableName;
            } elseif (is_array($tableName)) {
                $table = array_keys($tableName)[0] . ' AS ' . $tableName[array_keys($tableName)[0]];
            } else {
                throw new DatabaseException('mysqli join: 无效的参数 -> $tableName');
            }
        }
        if (empty($leftField)) {
            throw new DatabaseException('mysqli join: 缺少参数 -> $leftField');
        } else {
            if (!is_string($leftField)) {
                throw new DatabaseException('mysqli join: 无效的参数 -> $leftField');
            }
        }
        if (empty($rightField)) {
            throw new DatabaseException('mysqli join: 缺少参数 -> $rightField');
        } else {
            if (!is_string($rightField)) {
                throw new DatabaseException('mysqli join: 无效的参数 -> $rightField');
            }
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
        $num = func_num_args();
        $where = null;
        if ($num > 0) {
            if ($num === 1) {
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
            } elseif ($num === 3) {
                $where = $this->_parseWhere($args[0], $args[1], $args[2]);
            } else {
                throw new DatabaseException('mysqli where: 无效的参数');
            }
        } else {
            throw new DatabaseException('mysqli where: 缺少参数');
        }
        $this->_data['where'] = ' WHERE ' . $where;
        return $this;
    }
    private function _parseWhere($field, $condition, $value)
    {
        if (!is_string($field) || !is_string($condition)) {
            throw new DatabaseException('mysqli where: 无效的参数');
        }
        $matches = ['eq', 'neq', 'lk', 'nlk', 'bt', 'nbt', 'in', 'nin'];
        if (!in_array($condition, $matches)) {
            throw new DatabaseException('mysqli where: 无效的参数');
        }
        $condition = str_replace(
            $matches,
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
        $num = func_num_args();
        $order = null;
        if ($num > 0) {
            if ($num === 1) {
                if (is_string($args[0])) {
                    $order = $args[0] . ' ASC';
                } else {
                    throw new DatabaseException('mysqli order: 无效的参数');
                }
            } elseif ($num === 2) {
                if (is_string($args[0]) && is_string($args[1])) {
                    $order = $args[0] . ' ' . strtoupper($args[1]);
                } elseif (is_array($args[0]) && count($args[0]) === 2 && in_array(strtolower($args[0][1]), ['asc', 'desc']) && is_array($args[1]) && count($args[1]) === 2 && in_array(strtolower($args[1][1]), ['asc', 'desc'])) {
                    $order = $args[0][0] . ' ' . strtoupper($args[0][1]) . ', ' . $args[1][0] . ' ' . strtoupper($args[1][1]);
                } else {
                    throw new DatabaseException('mysqli order: 无效的参数');
                }
            } else {
                $array = [];
                foreach ($args as $arg) {
                    if (!is_array($arg) || count($arg) !== 2 || !in_array(strtolower($arg[1]), ['asc', 'desc'])) {
                        throw new DatabaseException('mysqli order: 无效的参数');
                    }
                    $array[] = $arg[0] . ' ' . strtoupper($arg[1]);
                }
                $order = implode(', ', $array);
            }
        } else {
            throw new DatabaseException('mysqli order: 缺少参数');
        }
        $this->_data['order'] = ' ORDER BY ' . $order;
        return $this;
    }
    public function limit()
    {
        $args = func_get_args();
        $num = func_num_args();
        $limit = null;
        if ($num > 0) {
            if ($num === 1) {
                $limit = $args[0];
            } elseif ($num === 2) {
                $limit = $args[0] . ', ' . $args[1];
            } else {
                throw new DatabaseException('mysqli limit: 参数数量有误');
            }
        } else {
            throw new DatabaseException('mysqli limit: 缺少参数');
        }
        $this->_data['limit'] = ' LIMIT ' . $limit;
        return $this;
    }
    public function select()
    {
        $field = isset($this->_data['field']) ? $this->_data['field'] : '*';
        if (!isset($this->_data['table'])) {
            throw new DatabaseException('mysqli select: 缺少表名');
        }
        $join = isset($this->_data['join']) ? $this->_data['join'] : '';
        $where = isset($this->_data['where']) ? $this->_data['where'] : '';
        $order = isset($this->_data['order']) ? $this->_data['order'] : '';
        $limit = isset($this->_data['limit']) ? $this->_data['limit'] : '';
        // mysqli 下的 SELECT 标准（未实现一些比如：UNION / GROUP BY / HAVING 等）
        $sql = 'SELECT ' . $field . ' FROM ' . $this->_data['table'] . $join . $where . $order . $limit;
        return $this->query($sql);
    }
    public function insert($data)
    {}
    public function update($data)
    {}
    public function delete()
    {}
    public function startTrans()
    {}
    public function rollback()
    {}
    public function commit()
    {}
}
final class DatabaseFactory
{
    private static $_driverInstanceArray = [];
    public static function getDriverInstance($driverName = null)
    {
        $driverName = $driverName === null ? C('db.driver') : $driverName;
        if (!isset(self::$_driverInstanceArray[$driverName])) {
            self::$_driverInstanceArray[$driverName] = new $driverName();
        }
        return self::$_driverInstanceArray[$driverName];
    }
}
class ExceptionAbstract extends \Exception {}
class DatabaseException extends ExceptionAbstract
{
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }
}
class UndefinedException extends ExceptionAbstract
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
class FileNotFoundException extends ExceptionAbstract
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
class SystemException extends ExceptionAbstract
{
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }
}
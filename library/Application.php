<?php
/**
 * Application.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/5 18:34
 */

final class Application
{
    /**
     * @var Application|null 当前对象
     */
    private static $_instance = null;

    /**
     * @var bool 是否渲染视图
     */
    private $_isViewRender = true;

    /**
     * @var Config|null 配置对象
     */
    private $_configInstance = null;

    /**
     * @var Request|null 请求对象
     */
    private $_requestInstance = null;

    /**
     * @var Response|null 响应对象
     */
    private $_responseInstance = null;

    /**
     * @var array 缓存驱动对象数组
     */
    private $_cacheInstanceArray = [];

    /**
     * @var array 数据库驱动对象数组
     */
    private $_databaseInstanceArray = [];

    /**
     * @var array 模型对象数组
     */
    private $_modelInstanceArray = [];

    /**
     * 获取当前类对象
     *
     * @return Application|null
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * 构造器
     */
    public function __construct()
    {
        $this->_configInstance = new Config();
        $this->_requestInstance = new Request();
        $this->_responseInstance = new Response();
    }

    /**
     * 关闭视图渲染
     *
     * @return Application
     */
    public function disableView()
    {
        $this->_isViewRender = false;
        return $this;
    }

    /**
     * 应用初始化
     *
     * 执行当前应用目录下的 Bootstrap 类中所有以 _init 开头的方法
     *
     * @return Application
     * @throws Exception
     */
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

    /**
     * 执行应用
     *
     * @throws Exception
     */
    public function run()
    {
        /**
         * 导入应用配置
         */
        $this->_configInstance->load(ROOT . '/application/module/' . MODULE . '/config/' . ENV . '.php');

        /**
         * 初始化 SESSION
         */
        if (SESSION_CACHE_ENABLE) {
            ini_set('session.gc_probability', 0);
            session_set_save_handler(new Session(), true);
        } else {
            ini_set('session.gc_probability', 1);
        }

        ini_set('session.auto_start', 0);
        session_start();

        /**
         * 执行路由
         */
        $routerInstance = new Router();
        $routerInstance->route();
        unset($routerInstance);

        /**
         * 执行分发
         */
        $controller = ucfirst($this->_requestInstance->getControllerName()) . 'Controller';
        $controllerFile = ROOT . '/application/module/' . MODULE . '/controller/' . $controller . '.php';
        if (!is_file($controllerFile)) {
            throw new \Exception($controllerFile, 10002);
        }
        require $controllerFile;
        if (!class_exists($controller, false)) {
            throw new \Exception($controller, 10003);
        }

        $controllerInstance = new $controller();
        $action = $this->_requestInstance->getActionName() . 'Action';
        if (!method_exists($controllerInstance, $action)) {
            throw new \Exception($action, 10004);
        }

        $ret = call_user_func([$controllerInstance, $action]);
        unset($controllerInstance);

        /**
         * 是否渲染视图
         */
        if ($this->_isViewRender) {
            $viewInstance = new View();
            $ret = $viewInstance->render($this->_requestInstance->getActionName() . '.php', $ret);
            unset($viewInstance);
        }
        $this->_responseInstance->setBody($ret);

        /**
         * 执行响应
         */
        $this->_responseInstance->output();
    }

    /**
     * 获取配置对象
     *
     * @return Config|null
     */
    public function getConfigInstance()
    {
        return $this->_configInstance;
    }

    /**
     * 获取请求对象
     *
     * @return Request|null
     */
    public function getRequestInstance()
    {
        return $this->_requestInstance;
    }

    /**
     * 获取响应对象
     *
     * @return Response|null
     */
    public function getResponseInstance()
    {
        return $this->_responseInstance;
    }

    /**
     * 获取缓存驱动对象
     *
     * @param string $driverName
     * @return CacheInterface
     */
    public function getCacheInstance($driverName = null)
    {
        $driverName = $driverName === null ? ucfirst(Application::getInstance()->getConfigInstance()->get('cache.driver')) : $driverName;

        if (!isset($this->_cacheInstanceArray[$driverName])) {
            $this->_cacheInstanceArray[$driverName] = new $driverName();
        }

        return $this->_cacheInstanceArray[$driverName];
    }

    /**
     * 获取数据库驱动对象
     *
     * @param string $driverName
     * @return DatabaseInterface
     */
    public function getDatabaseInstance($driverName = null)
    {
        $driverName = $driverName === null ? ucfirst(Application::getInstance()->getConfigInstance()->get('database.driver')) : $driverName;

        if (!isset($this->_databaseInstanceArray[$driverName])) {
            $this->_databaseInstanceArray[$driverName] = new $driverName();
        }

        return $this->_databaseInstanceArray[$driverName];
    }

    /**
     * 获取模型对象
     *
     * 使用：getModelInstance('user') 则创建 UserModel
     *
     * @param string $modelName
     * @return ModelAbstract
     * @throws Exception
     */
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
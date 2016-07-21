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
     * @var array 钩子对象数组
     */
    private $_hookInstanceArray = [];

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
     * 支持应用自身的初始化
     *
     * 当前 MODULE 应用目录下的 Bootstrap.php，执行其中所有以 _init 开头的方法
     *
     * 作用：
     *      1、定义一些应用常量，或者执行一些应用初始化操作
     *      2、另一个重要的功能：注册 Hook 对象（执行流程：new Application -> bootstrap -> run，run 中将执行埋好的钩子，所以只有在 bootstrap 这一步 registerHook 才能让钩子真正得到执行）
     *
     * 使用：
     *      library\Application::getInstance()->bootstrap()->run()
     *
     * @return Application
     * @throws FileNotFoundException
     * @throws UndefinedException
     */
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

        $bootstrapInstance = new Bootstrap();
        $methodArr = get_class_methods($bootstrapInstance);
        foreach ($methodArr as $method) {
            if (substr($method, 0, 5) === '_init') {
                $bootstrapInstance->$method();
            }
        }
        unset($bootstrapInstance);

        return $this;
    }

    /**
     * 执行应用
     *
     * @throws FileNotFoundException
     * @throws UndefinedException
     */
    public function run()
    {
        /**
         * 初始化 SESSION
         */
        if (SESSION_CACHE_ENABLE) {
            $sessionInstance = new Session();
            session_set_save_handler($sessionInstance, false);
        }

        // 如果开启了 cache 保存 SESSION，则关闭垃圾回收（通过 cache 自身的失效机制）
        if (SESSION_CACHE_ENABLE) {
            ini_set('session.gc_probability', 0);
        } else {
            ini_set('session.gc_probability', 1);
        }

        ini_set('session.auto_start', 0);
        session_start();

        // todo: beforeRoute Hook

        /**
         * 执行路由
         */
        $routerInstance = new Router();
        $routerInstance->route();
        unset($routerInstance);

        // todo: beforeDispatch Hook

        /**
         * 执行分发
         */
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

        /**
         * 是否渲染视图
         */
        if ($this->_isViewRender) {
            $viewInstance = new View();
            $ret = $viewInstance->render($this->_requestInstance->getActionName() . '.php', $ret);
            unset($viewInstance);
        }
        $this->_responseInstance->setBody($ret);

        // todo: beforeResponse Hook

        /**
         * 执行响应
         */
        $this->_responseInstance->response();
    }

    /**
     * 关闭视图渲染
     */
    public function disableView()
    {
        $this->_isViewRender = false;
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
        $driverName = $driverName === null ? ucfirst(C('cache.driver')) : $driverName;

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
        $driverName = $driverName === null ? ucfirst(C('database.driver')) : $driverName;

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
     * @throws FileNotFoundException
     * @throws UndefinedException
     */
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

    /**
     * 注册钩子对象
     *
     * 可以级联调用注册多个钩子，钩子执行次序同注册顺序：
     *      $application->registerHook(new AHook())->registerHook(new BHook())
     *
     * @param HookInterface $hookInstance
     * @return Application
     */
    public function registerHook($hookInstance)
    {
        $this->_hookInstanceArray[] = $hookInstance;

        return $this;
    }
}
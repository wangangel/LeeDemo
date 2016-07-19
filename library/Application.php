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
     * 默认的控制器名称
     */
    const DEFAULT_CONTROLLER_NAME = 'index';

    /**
     * 默认的动作名称
     */
    const DEFAULT_ACTION_NAME = 'index';

    /**
     * 控制器后缀
     */
    const CONTROLLER_SUFFIX = 'Controller';

    /**
     * 动作后缀
     */
    const ACTION_SUFFIX = 'Action';

    /**
     * @var Application|null 当前对象
     */
    private static $_instance = null;

    /**
     * @var bool 是否渲染视图
     */
    private $_isViewRender = true;

    /**
     * @var Request|null 请求对象
     */
    private $_requestInstance = null;

    /**
     * @var Response|null 响应对象
     */
    private $_responseInstance = null;

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

        $obj = new Bootstrap();
        $methodArr = get_class_methods($obj);
        foreach ($methodArr as $method) {
            if (substr($method, 0, 5) === '_init') {
                $obj->$method();
            }
        }

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
        // todo: beforeRoute Hook

        // 执行路由
        $routerInstance = new Router();
        $routerInstance->route();
        unset($routerInstance);

        // todo: beforeDispatch Hook

        // 执行分发
        $controller = ucfirst($this->_requestInstance->getControllerName()) . self::CONTROLLER_SUFFIX;
        $controllerFile = ROOT . '/application/module/' . MODULE . '/controller/' . $controller . '.php';
        if (!is_file($controllerFile)) {
            throw new FileNotFoundException($controllerFile, '控制器文件丢失');
        }
        require $controllerFile;

        $action = $this->_requestInstance->getActionName() . self::ACTION_SUFFIX;
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

    /**
     * 关闭视图渲染
     */
    public function disableView()
    {
        $this->_isViewRender = false;
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
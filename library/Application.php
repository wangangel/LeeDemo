<?php
/**
 * Application.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/5 18:34
 */

namespace library;

use library\exception\FileNotFoundException;

final class Application
{
    /**
     * @var Application|null 当前对象
     */
    private static $_instance = null;

    /**
     * @var array|null 配置数组
     */
    private $_config = null;

    /**
     * @var Request|null 请求对象
     */
    private $_requestInstance = null;

    /**
     * @var array 钩子对象数组
     */
    private $_hookInstanceArray = array();

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
        $this->_loadConfig();
        $this->_requestInstance = new Request();
    }

    /**
     * 加载配置
     *
     * 在 /application/config/ 下必须包含系统配置文件
     * 你也可以在 /application/module/[MODULE]/config/ 下定义应用配置文件，相同配置应用的优先级更高
     *
     * @throws FileNotFoundException
     */
    private function _loadConfig()
    {
        // 系统配置
        $systemConfigFile = ROOT . SEP . 'application' . SEP . 'config' . SEP . ENV . '.php';
        if (!is_file($systemConfigFile)) {
            throw new FileNotFoundException($systemConfigFile, '系统配置文件丢失');
        }
        $config = include $systemConfigFile;

        // 应用配置
        $moduleConfigFile = ROOT . SEP . 'application' . SEP . 'module' . SEP . MODULE . SEP . 'config' . SEP . ENV . '.php';
        if (is_file($moduleConfigFile)) {
            $applicationConfig = include $moduleConfigFile;
            $config = array_merge($config, $applicationConfig);
        }

        $this->_config = $config;
    }

    /**
     * 获取配置
     *
     * 使用：getConfig('system') / getConfig('db.master.host')，最多支持到三层
     *
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    public function getConfig($name)
    {
        if (!is_string($name)) {
            throw new \Exception('参数仅接受字符串: Application->getConfig()');
        }

        $ret = null;
        if (strpos($name, '.') > 0) {
            $array = explode('.', $name);
            switch (count($array)) {
                case 2:
                    $ret = isset($this->_config[$array[0]][$array[1]]) ? $this->_config[$array[0]][$array[1]] : null;
                    break;
                case 3:
                    $ret = isset($this->_config[$array[0]][$array[1]][$array[2]]) ? $this->_config[$array[0]][$array[1]][$array[2]] : null;
                    break;
                default:
                    break;
            }
        } else {
            $ret = isset($this->_config[$name]) ? $this->_config[$name] : null;
        }

        if (is_null($ret)) {
            throw new \Exception('配置不存在: ' . $name);
        }

        return $ret;
    }

    /**
     * 支持应用自身的初始化
     *
     * 当前 MODULE 应用目录下的 Bootstrap.php，执行其中所有以 _init 开头的方法，方法都将接收到 Application 对象作为参数
     *
     * 作用：
     *      1、定义一些应用常量，或者执行一些应用初始化操作
     *      2、另一个重要的功能：注册 Hook 对象（执行流程：new Application -> bootstrap -> run，run 中将执行埋好的钩子，所以只有在 bootstrap 这一步 registerHook 才能让钩子真正得到执行）
     *
     * 使用：
     *      library\Application::getInstance()->bootstrap()->run()
     *
     * @return object
     * @throws \Exception
     */
    public function bootstrap()
    {
        $bootstrapFile = ROOT . SEP . 'application' . SEP . 'module' . SEP . MODULE . SEP . 'Bootstrap.php';
        if (!is_file($bootstrapFile)) {
            throw new FileNotFoundException($bootstrapFile, '当前应用的 Bootstrap 文件未找到');
        }
        require $bootstrapFile;

        $class = 'application\\module\\' . MODULE . '\\Bootstrap';
        if (!class_exists($class, false)) {
            throw new \Exception('当前应用的 Bootstrap 文件存在，但类未定义: ' . $bootstrapFile);
        }

        $obj = new $class();
        $methodArr = get_class_methods($obj);
        foreach ($methodArr as $method) {
            if (substr($method, 0, 5) === '_init') {
                $obj->$method();
            }
        }

        return $this;
    }

    /**
     * 执行分发
     *
     * Action 都将接收到 Application 对象作为参数
     */
    public function run()
    {
        // todo: beforeRoute Hook

        $routerInstance = new Router();
        $routerInstance->route();
        unset($routerInstance);

        // todo: afterRoute Hook

        // todo: beforeDispatch Hook

        $class = 'application\\module\\' . MODULE . '\\controller\\' . $this->_requestInstance->getControllerName();
        $action = $this->_requestInstance->getActionName();
        (new $class())->$action();

        // todo: afterDispatch Hook
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
     * 注册钩子对象
     *
     * 可以级联调用注册多个钩子，钩子执行次序同注册顺序：
     *      $application->registerHook(new AHook())->registerHook(new BHook())
     *
     * @param HookInterface $hookInstance
     * @return object
     */
    public function registerHook(HookInterface $hookInstance)
    {
        $this->_hookInstanceArray[] = $hookInstance;

        return $this;
    }

    /**
     * 获取钩子对象数组
     *
     * @return array
     */
    public function getHookInstanceArray()
    {
        return $this->_hookInstanceArray;
    }
}
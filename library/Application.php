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
     * @var Application|null 当前类对象
     */
    private static $_instance = null;

    /**
     * @var array|null 配置数组
     */
    private $_config = null;

    /**
     * @var Dispatcher|null 分发器对象
     */
    private $_dispatcherInstance = null;

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
        $this->_checkDefinition();
        $this->_loadConfig();
        $this->_bootstrap();
        $this->_dispatcherInstance = new Dispatcher();
    }

    /**
     * 检查用户定义的常量
     */
    private function _checkDefinition()
    {
        // 检查 ENV
        if (!defined('ENV')) {
            throw new \Exception('常量 ENV 未定义');
        } else {
            if (!in_array(ENV, ['development', 'test', 'production'], true)) {
                throw new \Exception('常量 ENV 只能定义以下值中的一个: development / test / production');
            }
        }

        // 检查 MODULE
        if (!defined('MODULE')) {
            throw new \Exception('常量 MODULE 未定义');
        } else {
            $moduleDir = ROOT . SEP . 'application' . SEP . 'module' . SEP . MODULE;
            if (!is_dir($moduleDir)) {
                throw new \Exception('常量 MODULE 指定的应用目录不存在: ' . $moduleDir);
            }
        }
    }

    /**
     * 加载配置
     *
     * @throws FileNotFoundException
     */
    private function _loadConfig()
    {
        // 在 /application/config 下会包含系统配置文件（必须有）
        $systemConfigFile = ROOT . SEP . 'application' . SEP . 'config' . SEP . ENV . '.php';
        if (!is_file($systemConfigFile)) {
            throw new FileNotFoundException($systemConfigFile, '系统配置文件丢失');
        }
        $config = include $systemConfigFile;

        // 如果当前应用下也定义了配置文件，则相同配置优先级更高
        $moduleConfigFile = ROOT . SEP . 'application' . SEP . 'module' . SEP . MODULE . SEP . 'config' . SEP . ENV . '.php';
        if (is_file($moduleConfigFile)) {
            $applicationConfig = include $moduleConfigFile;
            $config = array_merge($config, $applicationConfig);
        }

        $this->_config = $config;
    }

    /**
     * 支持应用自身的初始化
     *
     * 在当前 MODULE 目录下放入 Bootstrap.php，则 Bootstrap 类中以 _init 开头的方法都将得到执行
     *
     * @throws \Exception
     */
    private function _bootstrap()
    {
        $bootstrapFile = ROOT . SEP . 'application' . SEP . 'module' . SEP . MODULE . SEP . 'Bootstrap.php';
        if (is_file($bootstrapFile)) {
            require $bootstrapFile;

            $class = 'application\\module\\' . MODULE . '\\Bootstrap';
            if (!class_exists($class, false)) {
                throw new \Exception('当前应用 Bootstrap 文件存在，但类未定义: ' . $bootstrapFile);
            }

            $obj = new $class();
            $methodArr = get_class_methods($obj);
            foreach ($methodArr as $method) {
                if (substr($method, 0, 5) === '_init') {
                    $obj->$method();
                }
            }
        }
    }

    /**
     * 执行分发
     */
    public function run()
    {
        $this->_dispatcherInstance->dispatch();
    }

    /**
     * 获取分发器对象
     *
     * @return Dispatcher|null
     */
    public function getDispatcherInstance()
    {
        return $this->_dispatcherInstance;
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
}
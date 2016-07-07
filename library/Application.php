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
     * @var Config|null 配置对象
     */
    private $_configInstance = null;

    /**
     * @var Dispatcher|null 分发器对象
     */
    private $_dispatcherInstance = null;

    /**
     * @var bool 阻止 run() 的重复调用
     */
    private $_isRunning = false;

    /**
     * 构造器
     */
    public function __construct()
    {
        $this->_checkDefinition();
        $this->_registerPsr4();
        $this->_configInstance = new Config($this->_loadConfig());
        $this->_dispatcherInstance = Dispatcher::getInstance();
    }

    /**
     * 销毁
     */
    public function __destruct()
    {
        self::$_instance = null;
    }

    /**
     * 禁止序列化
     */
    private function __sleep() {}

    /**
     * 禁止反序列化
     */
    private function __wakeup() {}

    /**
     * 禁止克隆
     */
    private function __clone() {}

    /**
     * 获取当前类的对象
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
     * 获取配置对象
     *
     * @return Config|null
     */
    public function getConfigInstance()
    {
        return $this->_configInstance;
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
     * 执行应用
     */
    public function run()
    {
        if ($this->_isRunning === true) {
            throw new \Exception('禁止重复调用 Application->run()');
        }

        $this->_isRunning = true;
        $this->_dispatcherInstance->dispatch();
    }

    /**
     * 检查必要的常量定义
     *
     * 这些常量必须在入口文件中定义，否则系统无法正常运行
     */
    private function _checkDefinition()
    {
        if (!defined('ENV')) {
            throw new \Exception('常量 ENV 未定义');
        }
        if (!defined('MODULE')) {
            throw new \Exception('常量 MODULE 未定义');
        }
        if (!defined('ROOT')) {
            throw new \Exception('常量 ROOT 未定义');
        }
    }

    /**
     * 注册 PSR-4 autoload
     */
    private function _registerPsr4()
    {
        spl_autoload_register(function($className) {
            $file = ROOT . SEP . str_replace('\\', SEP, $className) . '.php';
            if (!is_file($file)) {
                throw new \Exception('无法加载类文件: ' . $file);
            }
            require_once $file;
            if (!class_exists($className, false) && !interface_exists($className, false)) {
                if (strpos($className, 'Interface') > 0) {
                    throw new \Exception('接口未定义: ' . $className);
                } else {
                    throw new \Exception('类未定义: ' . $className);
                }
            }
        });
    }

    /**
     * 加载配置
     *
     * @return array
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

        // 如果当前 MODULE 下也定义了配置文件，则相同配置优先级更高
        $moduleConfigFile = ROOT . SEP . 'application' . SEP . 'module' . SEP . MODULE . SEP . 'config' . SEP . ENV . '.php';
        if (is_file($moduleConfigFile)) {
            $applicationConfig = include $moduleConfigFile;
            $config = array_merge($config, $applicationConfig);
        }

        return $config;
    }
}
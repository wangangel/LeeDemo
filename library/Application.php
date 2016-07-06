<?php
/**
 * Application.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/5 18:34
 */

namespace library;

use library\exception\CodeException;
use library\exception\FileNotFoundException;

final class Application
{
    /**
     * @var Application|null 当前类的对象
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
     * __construct
     */
    public function __construct()
    {
        $this->_configInstance = new Config($this->_loadConfig());
        $this->_dispatcherInstance = new Dispatcher();
    }

    /**
     * __destruct
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
     * 开始分发
     */
    public function run()
    {
        if ($this->_isRunning === true) {
            throw new CodeException('an application instance already run');
        }

        $this->_isRunning = true;
        $this->_dispatcherInstance->dispatch();
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
        $systemConfigFile = ROOT . '/application/config/' . ENV . '.php';
        if (!is_file($systemConfigFile)) {
            throw new FileNotFoundException($systemConfigFile, 'system config file not found');
        }
        $config = include $systemConfigFile;

        // 如果当前 MODULE 下也定义了配置文件，则相同配置优先级更高
        $moduleConfigFile = ROOT . '/application/module/' . MODULE . '/config/' . ENV . '.php';
        if (is_file($moduleConfigFile)) {
            $applicationConfig = include $moduleConfigFile;
            $config = array_merge($config, $applicationConfig);
        }

        return $config;
    }
}
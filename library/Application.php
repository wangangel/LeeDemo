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
        $this->_loadConfig();
        $this->_dispatcherInstance = new Dispatcher();
    }

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
            throw new \Exception('禁止重复调用: Application->run()');
        }

        $this->_isRunning = true;
        $this->_dispatcherInstance->dispatch();
    }

    /**
     * 检查用户需要定义的常量
     */
    private function _checkDefinition()
    {
        if (!defined('ENV')) {
            throw new \Exception('常量未定义: ENV');
        }
        if (!defined('MODULE')) {
            throw new \Exception('常量未定义: MODULE');
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
}
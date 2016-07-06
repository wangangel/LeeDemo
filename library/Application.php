<?php
/**
 * Application.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/5 18:34
 */

namespace library;

final class Application
{
    private static $_this = null;

    private $_configInstance = null;

    private $_dispatcherInstance = null;

    private $_modules = '';

    private $_isRunning = false;

    /**
     * 构造器
     */
    public function __construct()
    {

    }

    /**
     * 销毁
     */
    public function __destruct()
    {
        self::$_this = null;
    }

    public function __sleep() {}
    public function __wakeup() {}
    public function __clone() {}

    public function run()
    {
        echo '123333';
    }
}
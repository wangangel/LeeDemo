<?php
/**
 * Dispatcher.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/6 16:38
 */

namespace library;

final class Dispatcher
{
    /**
     * @var Dispatcher|null 当前类对象
     */
    private static $_instance = null;

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
     * 获取当前类对象
     *
     * @return Dispatcher|null
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function dispatch()
    {
        echo $_SERVER['REQUEST_METHOD'];
    }
}
<?php
/**
 * Session.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/16 9:22
 */

namespace library;

class Session
{
    /**
     * @var Session|null 当前对象
     */
    private static $_instance = null;

    /**
     * 构造器
     */
    public function __construct()
    {
        session_start();
        ini_set('session.save_handler', SESSION_SAVE_HANDLER);
    }

    /**
     * 获取单例对象
     *
     * @return Session|null
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}
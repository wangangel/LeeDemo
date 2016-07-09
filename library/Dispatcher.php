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
     * @var Request|null 请求对象
     */
    private $_requestInstance = null;

    /**
     * @var array 插件对象数组
     */
    private $_hookInstanceArr = [];

    /**
     * 构造器
     */
    public function __construct()
    {
        $this->_requestInstance = new Request();
    }

    /**
     * 注册钩子对象
     */
    public function registerHook()
    {

    }

    /**
     * 执行分发
     *
     * 1、
     */
    public function dispatch()
    {

        // beforeRoute 钩子

        // afterRoute 钩子

        // beforeAction 钩子

        // afterAction 钩子


        echo $_SERVER['REQUEST_METHOD'];
    }
}
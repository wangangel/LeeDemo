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
     * 构造器
     */
    public function __construct()
    {
        $this->_requestInstance = new Request();
    }

    /**
     * 执行分发
     */
    public function dispatch()
    {
        echo $_SERVER['REQUEST_METHOD'];
    }
}
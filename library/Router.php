<?php
/**
 * Router.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/8 16:28
 */

final class Router
{
    /**
     * 默认的控制器名称
     */
    const DEFAULT_CONTROLLER_NAME = 'index';

    /**
     * 默认的动作名称
     */
    const DEFAULT_ACTION_NAME = 'index';

    /**
     * 最简单的路由
     */
    public function route()
    {
        $controllerName = I('get', 'c', self::DEFAULT_CONTROLLER_NAME, '/^[a-z]+$/');
        $actionName = I('get', 'a', self::DEFAULT_ACTION_NAME, '/^[a-zA-Z]+$/');

        Application::getInstance()->getRequestInstance()->setControllerName($controllerName)->setActionName($actionName);
    }
}
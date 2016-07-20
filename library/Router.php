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
        $requestInstance = Application::getInstance()->getRequestInstance();

        $controllerName = $requestInstance->getGlobalQuery('c', self::DEFAULT_CONTROLLER_NAME, '/^[a-z]+$/');
        $actionName = $requestInstance->getGlobalQuery('a', self::DEFAULT_ACTION_NAME, '/^[a-zA-Z]+$/');

        $requestInstance->setControllerName($controllerName)->setActionName($actionName);
    }
}
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
     * 最简单的路由
     */
    public function route()
    {
        $requestInstance = Application::getInstance()->getRequestInstance();

        $controllerName = $requestInstance->getGlobalQuery('c', Application::DEFAULT_CONTROLLER_NAME, '/^[a-z]+$/');
        $actionName = $requestInstance->getGlobalQuery('a', Application::DEFAULT_ACTION_NAME, '/^[a-zA-Z]+$/');

        $requestInstance->setControllerName($controllerName)->setActionName($actionName);
    }
}
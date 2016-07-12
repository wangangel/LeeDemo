<?php
/**
 * Router.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/8 16:28
 */

namespace library;

final class Router
{
    /**
     * 最简单的路由
     */
    public function route()
    {
        $requestInstance = Application::getInstance()->getRequestInstance();

        $controllerName = $requestInstance->getGlobalQuery('c', G::DEFAULT_CONTROLLER, '/^[a-z]+$/');
        $actionName = $requestInstance->getGlobalQuery('a', G::DEFAULT_ACTION, '/^[a-zA-Z]+$/');

        $requestInstance->setControllerName($controllerName)->setActionName($actionName);
    }
}
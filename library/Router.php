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

        $controllerName = ucfirst($requestInstance->getGlobalQuery('c', G::defaultController, '/^[a-z]+$/')) . G::controllerSuffix;
        $actionName = $requestInstance->getGlobalQuery('a', G::defaultAction, '/^[a-zA-Z]+$/') . G::actionSuffix;

        $requestInstance->setControllerName($controllerName)
            ->setActionName($actionName);
    }
}
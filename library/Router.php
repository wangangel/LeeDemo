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
     *
     * @param Request $requestInstance
     */
    public function route(Request $requestInstance)
    {
        $controllerName = $requestInstance->getGlobalVariable('get', 'c', self::DEFAULT_CONTROLLER_NAME);
        $actionName = $requestInstance->getGlobalVariable('get', 'a', self::DEFAULT_ACTION_NAME);

        $controllerName = preg_match('/^[a-z]+$/', $controllerName) ? $controllerName : self::DEFAULT_CONTROLLER_NAME;
        $actionName = preg_match('/^[a-zA-Z]+$/', $actionName) ? $actionName : self::DEFAULT_ACTION_NAME;

        $requestInstance->setControllerName($controllerName)->setActionName($actionName);
    }
}
<?php
/**
 * PluginInterface.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/9 14:45
 */

namespace library;

interface HookInterface
{
    /**
     * 执行路由前
     *
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function beforeRoute(Request $request, Response $response);

    /**
     * 执行路由后
     *
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function afterRoute(Request $request, Response $response);

    /**
     * 执行 action 前
     *
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function beforeAction(Request $request, Response $response);

    /**
     * 执行 action 后
     *
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function afterAction(Request $request, Response $response);
}
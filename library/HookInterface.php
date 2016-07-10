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
     * @param Application $applicationInstance
     * @return mixed
     */
    public function beforeRoute(Application $applicationInstance);

    /**
     * 执行路由后
     *
     * @param Application $applicationInstance
     * @return mixed
     */
    public function afterRoute(Application $applicationInstance);

    /**
     * 执行 action 前
     *
     * @param Application $applicationInstance
     * @return mixed
     */
    public function beforeAction(Application $applicationInstance);

    /**
     * 执行 action 后
     *
     * @param Application $applicationInstance
     * @return mixed
     */
    public function afterAction(Application $applicationInstance);
}
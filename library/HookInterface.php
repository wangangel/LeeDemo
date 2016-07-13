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
     * 路由前
     *
     * @return mixed
     */
    public function beforeRoute();

    /**
     * 分发前
     *
     * @return mixed
     */
    public function beforeDispatch();

    /**
     * 渲染前
     *
     * @return mixed
     */
    public function beforeRender();

    /**
     * 响应前
     *
     * @return mixed
     */
    public function beforeResponse();
}
<?php
/**
 * IndexController.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/10 14:12
 */

namespace application\module\www\controller;

use library\Application;
use library\ControllerAbstract;

class IndexController extends ControllerAbstract
{
    /**
     * 首页
     *
     * @param Application $applicationInstance
     */
    public function indexAction(Application $applicationInstance)
    {
        var_dump($applicationInstance);
    }
}
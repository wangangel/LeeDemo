<?php
/**
 * BlogController.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/16 13:52
 */

namespace application\module\www\controller;

use library\ControllerAbstract;
use library\Application;
use application\model\UserModel;

class BlogController extends ControllerAbstract
{
    /**
     * 构造器
     */
    public function __constrcut()
    {

    }

    /**
     * 首页
     */
    public function indexAction()
    {
        (new UserModel())->test();
    }

    /**
     * 日志列表
     */
    public function postListAction()
    {

    }

    /**
     * 日志正文
     */
    public function postAction()
    {

    }
}
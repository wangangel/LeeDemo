<?php
/**
 * BlogController.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/16 13:52
 */

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
        M('user')->test();
    }

    /**
     * 日志列表
     */
    public function postListAction()
    {
        $data = M('post')->getPagedList();

        return [
            'data' => $data
        ];
    }

    /**
     * 日志正文
     */
    public function postAction()
    {

    }
}
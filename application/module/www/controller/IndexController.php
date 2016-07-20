<?php
/**
 * IndexController.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/10 14:12
 */

class IndexController extends ControllerAbstract
{
    /**
     * 首页
     */
    public function indexAction()
    {
        $sess = Session::getInstance();

        return array(
            'aaa' => '23432432',
            'bbb' => 'assdsad',
            'ccc' => 'dgsdfgfds',
            'ddd' => 'sdfgfdsggsg'
        );
    }
}
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
        $_SESSION['user'] = [
            'id' => 1,
            'email' => '670554666@qq.com',
            'nickname' => 'adf',
            'status' => 2
        ];
    }
}
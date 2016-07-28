<?php
/**
 * AccessController.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/28 10:16
 */

class AccessController extends ControllerAbstract
{
    /**
     * 注册
     */
    public function registerAction()
    {
        // view
    }

    /**
     * 注册 - 提交
     */
    public function registerSubmitAction()
    {

    }

    /**
     * 登陆
     */
    public function loginAction()
    {

    }

    /**
     * 登陆 - 提交
     */
    public function loginSubmitAction()
    {

    }

    /**
     * 退出
     */
    public function logoutAction()
    {
        Application::getInstance()->disableView();

        unset($_SESSION['user']);
        session_destroy();

        Application::getInstance()->getResponseInstance()->setRedirect('/');
    }
}
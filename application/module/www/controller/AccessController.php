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
        // 已登陆
        if (isset($_SESSION['user'])) {
            Application::getInstance()->disableView()->getResponseInstance()->setRedirect('/');
        }
    }

    /**
     * 注册 - 发送邮件
     */
    public function registerMailSendAction()
    {
        Application::getInstance()->disableView();

        $email = Application::getInstance()->getRequestInstance()->getGlobalVariable('post', 'email', null, '/^([a-zA-Z0-9_\.\-]+)\@(qq|163)\.com$/');
        $captcha = Application::getInstance()->getRequestInstance()->getGlobalVariable('post', 'captcha', null, '/^[a-z0-9]{5}$/');

        // 已登陆
        if (isset($_SESSION['user'])) {
            Application::getInstance()->getResponseInstance()->setRedirect('/');
        }

        // 邮箱验证
        if ($email === null) {
            throw new HttpException(404, '无效的邮箱地址');
        }

        // 验证码验证
        if ($email !== null && strtolower($captcha) !== $_SESSION['captcha']) {
            throw new HttpException(404, '验证码校验失败');
        }
        $_SESSION['captcha'] = null;

        // 发送邮件
        $token = md5(uniqid(md5(microtime(true) . mt_rand(10000, 99999))));
        $url = 'http://sample.localhost/?c=access&a=registerVerify&token=' . $token;
        $mailer = mailer(
            $email,
            'XX网：请验证您的邮箱',
            '<p>亲爱的 ' . $email . '，欢迎注册XX网！点击下面的链接验证您的邮箱：</p><p><a href="' . $url . '">' . $url . '</a></p><p>不能点击请拷贝链接地址到浏览器中打开。</p>'
        );
        if ($mailer) {
            $_SESSION['emailVerify'] = [
                'email' => $email,
                'token' => $token,
            ];
        }

        return [
            'mailer' => $mailer
        ];
    }

    /**
     * 注册 - 邮件验证并完善密码昵称等信息
     */
    public function registerVerifyAction()
    {
        return $_SESSION['emailVerify'];
    }

    /**
     * 注册 - 完成
     */
    public function registerVerifySubmitAction()
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
        session_destroy();
        $this->redirect('/');
    }
}
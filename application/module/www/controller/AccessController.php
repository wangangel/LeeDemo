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
            $this->redirect('/');
        }
    }

    /**
     * 注册 - 发送邮件
     */
    public function registerMailSendAction()
    {
        $email = Application::getInstance()->getRequestInstance()->getGlobalVariable('post', 'email', null, '/^([a-zA-Z0-9_\.\-]+)\@(qq|163)\.com$/');
        $captcha = Application::getInstance()->getRequestInstance()->getGlobalVariable('post', 'captcha', null, '/^[a-z0-9]{5}$/');

        // 已登陆
        if (isset($_SESSION['user'])) {
            $this->redirect('/');
        }

        // 邮箱验证
        if ($email === null) {
            throw new HttpException(404, '无效的邮箱地址');
        }

        // 验证码验证
        if ($captcha === null || $_SESSION['captcha'] === null || strtolower($captcha) !== $_SESSION['captcha']) {
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

        return $this->json([
            'mailer' => $mailer
        ]);
    }

    /**
     * 注册 - 邮件验证并完善密码昵称等信息
     */
    public function registerVerifyAction()
    {
        // 已登陆
        if (isset($_SESSION['user'])) {
            $this->redirect('/');
        }

        $tokenGet = Application::getInstance()->getRequestInstance()->getGlobalVariable('get', 'token', null);

        return [
            'emailVerify' => $_SESSION['emailVerify'],
            'tokenGet' => $tokenGet
        ];
    }

    /**
     * 注册 - 完成
     */
    public function registerVerifySubmitAction()
    {
        // 已登陆
        if (isset($_SESSION['user'])) {
            $this->redirect('/');
        }

        // emailVerify session 过期
        if (empty($_SESSION['emailVerify'])) {
            throw new HttpException(404, '验证邮件已过期');
        }
        $emailVerify = $_SESSION['emailVerify'];

        $password = Application::getInstance()->getRequestInstance()->getGlobalVariable('post', 'password', null, '/^[\@A-Za-z0-9\!\#\$\%\^\&\*\.\~]{6,15}$/');
        $nickname = Application::getInstance()->getRequestInstance()->getGlobalVariable('post', 'nickname', null, '/^[^\s]+$/');

        // 密码验证
        if ($password === null) {
            throw new HttpException(404, '密码格式有误');
        }

        // 昵称验证
        if ($nickname === null) {
            throw new HttpException(404, '昵称格式有误');
        }

        // user 入库
        $salt = md5(uniqid(md5(mt_rand(10000, 99999))));
        $password = md5($password . $salt);
        $userId = Application::getInstance()->getModelInstance('user')->addOne($emailVerify['email'], $password, $salt, $nickname);
        if ($userId === false) {
            throw new HttpException(404, '注册失败');
        }

        // 清除 emailVerify session
        $_SESSION['emailVerify'] = null;

        // user session
        $_SESSION['user'] = [
            'email' => $emailVerify['email'],
            'nickname' => $nickname
        ];

        return $this->json([
            'userId' => $userId
        ]);
    }

    /**
     * 登陆
     */
    public function loginAction()
    {

    }

    /**
     * 登陆 - 提交
     *
     * @param Request $requestInstance
     * @return string
     */
    public function loginSubmitAction(Request $requestInstance)
    {
        $email = $requestInstance->getGlobalVariable('post', 'email', null, '/^([a-zA-Z0-9_\.\-]+)\@(qq|163)\.com$/');
        $password = $requestInstance->getGlobalVariable('post', 'password', null, '/^.{5,15}$/');
        $captcha = $requestInstance->getGlobalVariable('post', 'captcha', null, '/^.{5,15}$/');

        return $this->json([
            'email' => $email,
            'password' => $password,
            'captcha' => $captcha
        ]);
    }

    /**
     * 退出
     */
    public function logoutAction()
    {
        session_destroy();
        return $this->redirect('/');
    }
}
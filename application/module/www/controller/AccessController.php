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
     *
     * @param Request $requestInstance
     * @param Response $responseInstance
     */
    public function registerAction(Request $requestInstance, Response $responseInstance)
    {
        // 已登陆
        if (isset($_SESSION['user'])) {
            $this->redirect($responseInstance, '/');
        }
    }

    /**
     * 注册 - 发送邮件
     *
     * @param Request $requestInstance
     * @param Response $responseInstance
     * @return string
     * @throws Exception
     */
    public function registerMailSendAction(Request $requestInstance, Response $responseInstance)
    {
        // 已登陆
        if (isset($_SESSION['user'])) {
            $this->redirect($responseInstance, '/');
        }

        // post
        $email = $requestInstance->getGlobalVariable('post', 'email', '');
        $captcha = $requestInstance->getGlobalVariable('post', 'captcha', '');

        // 验证码校验
        if (!$this->captchaCheck($captcha)) {
            throw new \Exception('验证码校验失败', 10019);
        }

        // 发送邮件
        $mailer = Application::getInstance()->getServiceInstance('access')->sendRegisterMail($email);

        return $this->json($responseInstance, [
            'mailer' => $mailer
        ]);
    }

    /**
     * 注册 - 邮件验证并完善密码昵称等信息
     *
     * @param Request $requestInstance
     * @param Response $responseInstance
     * @return array
     * @throws Exception
     */
    public function registerVerifyAction(Request $requestInstance, Response $responseInstance)
    {
        // 已登陆
        if (isset($_SESSION['user'])) {
            $this->redirect($responseInstance, '/');
        }

        // get
        $tokenGet = $requestInstance->getGlobalVariable('get', 'token', '', '/^[a-z0-9]{32}$/');

        return [
            'emailVerify' => $_SESSION['emailVerify'],
            'tokenGet' => $tokenGet
        ];
    }

    /**
     * 注册 - 完成
     *
     * @param Request $requestInstance
     * @param Response $responseInstance
     * @return string
     * @throws Exception
     */
    public function registerVerifySubmitAction(Request $requestInstance, Response $responseInstance)
    {
        // 已登陆
        if (isset($_SESSION['user'])) {
            $this->redirect($responseInstance, '/');
        }

        // post
        $password = $requestInstance->getGlobalVariable('post', 'password', '');
        $nickname = $requestInstance->getGlobalVariable('post', 'nickname', '');

        // 完成注册
        $userId = Application::getInstance()->getServiceInstance('access')->finishRegister($password, $nickname);

        return $this->json($responseInstance, [
            'userId' => $userId
        ]);
    }

    /**
     * 登陆
     *
     * @param Request $requestInstance
     * @param Response $responseInstance
     */
    public function loginAction(Request $requestInstance, Response $responseInstance)
    {
        // 已登陆
        if (isset($_SESSION['user'])) {
            $this->redirect($responseInstance, '/');
        }
    }

    /**
     * 登陆 - 提交
     *
     * @param Request $requestInstance
     * @param Response $responseInstance
     * @return string
     * @throws Exception
     */
    public function loginSubmitAction(Request $requestInstance, Response $responseInstance)
    {
        // 已登陆
        if (isset($_SESSION['user'])) {
            throw new \Exception('当前已经处于登录状态', 10023);
        }

        // post
        $email = $requestInstance->getGlobalVariable('post', 'email', '');
        $password = $requestInstance->getGlobalVariable('post', 'password', '');
        $captcha = $requestInstance->getGlobalVariable('post', 'captcha', '');

        // 验证码校验
        if (!$this->captchaCheck($captcha)) {
            throw new \Exception('验证码校验失败', 10019);
        }

        // 登录
        $userId = Application::getInstance()->getServiceInstance('access')->loginByEmailAndPassword($email, $password);

        return $this->json($responseInstance, [
            'userId' => $userId
        ]);
    }

    /**
     * 退出
     *
     * @param Request $requestInstance
     * @param Response $responseInstance
     * @return bool
     */
    public function logoutAction(Request $requestInstance, Response $responseInstance)
    {
        $_SESSION['user'] = null;
        session_destroy();
        return $this->redirect($responseInstance, '/');
    }
}
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
        $email = $requestInstance->getGlobalVariable('post', 'email', null, '/^([a-zA-Z0-9_\.\-]+)\@(qq|163)\.com$/');
        $captcha = $requestInstance->getGlobalVariable('post', 'captcha', null, '/^[a-z0-9]{5}$/');

        if ($email === null || $captcha === null) {
            throw new \Exception('输入内容不完整', 10024);
        }

        // 验证码验证
        if ($_SESSION['captcha'] === null || $_SESSION['captcha'] !== strtolower($captcha)) {
            throw new \Exception('验证码校验失败', 10019);
        }
        $_SESSION['captcha'] = null;

        // email 已注册验证
        $user = Application::getInstance()->getModelInstance('user')->getByEmail($email);
        if ($user !== false && !empty($user)) {
            throw new \Exception('该邮箱已被使用', 10025);
        }

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

        // url token
        $tokenGet = $requestInstance->getGlobalVariable('get', 'token', null, '/^[a-z0-9]{32}$/');

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

        // emailVerify session 过期
        if (empty($_SESSION['emailVerify'])) {
            throw new \Exception('验证邮件已过期', 10026);
        }
        $emailVerify = $_SESSION['emailVerify'];

        // post
        $password = $requestInstance->getGlobalVariable('post', 'password', null, '/^[\@A-Za-z0-9\!\#\$\%\^\&\*\.\-\_\~]{5,15}$/');
        $nickname = $requestInstance->getGlobalVariable('post', 'nickname', null, '/^[^\s]{2,15}$/');

        if ($password === null || $nickname === null) {
            throw new \Exception('输入内容不完整', 10024);
        }

        $password = md5($password);
        $nickname = mb_substr(htmlspecialchars($nickname), 0, 15, 'utf-8');

        // nickname 已注册验证
        $user = Application::getInstance()->getModelInstance('user')->getByNickname($nickname);
        if ($user !== false && !empty($user)) {
            throw new \Exception('该昵称已被使用', 10028);
        }

        // user 入库
        $userId = Application::getInstance()->getModelInstance('user')->addOne($emailVerify['email'], $password, $nickname);
        if ($userId === false) {
            throw new \Exception('注册失败', 10027);
        }

        // 清除 emailVerify session
        $_SESSION['emailVerify'] = null;

        // 设置登录 session
        $_SESSION['user'] = [
            'email' => $emailVerify['email'],
            'nickname' => $nickname
        ];

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
        $email = $requestInstance->getGlobalVariable('post', 'email', null, '/^([a-zA-Z0-9_\.\-]+)\@(qq|163)\.com$/');
        $password = $requestInstance->getGlobalVariable('post', 'password', null, '/^.{5,15}$/');
        $captcha = $requestInstance->getGlobalVariable('post', 'captcha', null, '/^[a-z0-9]{5}$/');

        if ($email === null || $password === null || $captcha === null) {
            throw new \Exception('输入内容不完整', 10024);
        }

        $password = md5($password);

        // 验证码验证
        if ($_SESSION['captcha'] === null || $_SESSION['captcha'] !== strtolower($captcha)) {
            throw new \Exception('验证码校验失败', 10019);
        }
        $_SESSION['captcha'] = null;

        // 用户是否存在
        $user = Application::getInstance()->getModelInstance('user')->getByEmailAndPassword($email, $password);
        if ($user === false) {
            throw new \Exception('帐号查询失败', 10020);
        }
        if (empty($user)) {
            throw new \Exception('用户名或密码有误', 10021);
        }
        if (intval($user['status']) !== UserModel::STATUS_NORMAL) {
            throw new \Exception('该帐号存在异常', 10022);
        }

        // 设置登录 session
        $_SESSION['user'] = $user;

        return $this->json($responseInstance, [
            'email' => $email
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
        session_destroy();
        return $this->redirect($responseInstance, '/');
    }
}
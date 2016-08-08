<?php
/**
 * AccessService.php
 *
 * User: 670554666@qq.com
 * Date: 2016/8/7 19:31
 */

class AccessService extends ServiceAbstract
{
    /**
     * 发送注册邮件
     *
     * @param string $email
     * @return bool
     * @throws Exception
     */
    public function sendRegisterMail($email)
    {
        if (!is_string($email) || !preg_match('/^([a-zA-Z0-9_\.\-]+)\@(qq|163)\.com$/', $email)) {
            throw new \Exception('无效的邮箱地址', 10041);
        }

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

        return true;
    }

    /**
     * 通过邮件里的链接完成注册
     *
     * @param string $password
     * @param string $nickname
     * @return int
     * @throws Exception
     */
    public function finishRegister($password, $nickname)
    {
        if (!is_string($password) || !preg_match('/^[\@A-Za-z0-9\!\#\$\%\^\&\*\.\-\_\~]{5,15}$/', $password)) {
            throw new \Exception('无效的密码', 10042);
        }
        if (!is_string($nickname) || !preg_match('/^[^\s]{2,15}$/', $nickname)) {
            throw new \Exception('无效的昵称', 10043);
        }

        // emailVerify session 过期
        if (empty($_SESSION['emailVerify'])) {
            throw new \Exception('验证邮件已过期', 10026);
        }
        $emailVerifySession = $_SESSION['emailVerify'];

        // 安全过滤
        $password = md5($password);
        $nickname = mb_substr(htmlspecialchars($nickname), 0, 15, 'utf-8');

        // nickname 已注册验证
        $user = Application::getInstance()->getModelInstance('user')->getByNickname($nickname);
        if ($user !== false && !empty($user)) {
            throw new \Exception('该昵称已被使用', 10028);
        }

        // user 入库
        $userId = Application::getInstance()->getModelInstance('user')->addOne([
            'email' => $emailVerifySession['email'],
            'password' => $password,
            'nickname' => $nickname,
            'access_visit' => 1,
            'access_comment' => 1,
            'access_message' => 1,
            'access_transfer' => 1,
            'status' => 2,
            'time_register' => time()
        ]);
        if ($userId === false) {
            throw new \Exception('注册失败', 10027);
        }

        // 注册成功则销毁 emailVerify session，防止利用有效的 session 无限注册
        $_SESSION['emailVerify'] = null;

        // 设置登录用户的 session
        $_SESSION['user'] = [
            'id' => $userId,
            'email' => $emailVerifySession['email'],
            'nickname' => $nickname
        ];

        return $userId;
    }

    /**
     * 根据 email 和 password 登录
     *
     * @param string $email
     * @param string $password 请注意传递进来的密码是未加密的
     * @return int
     * @throws Exception
     */
    public function loginByEmailAndPassword($email, $password)
    {
        if (!is_string($email) || !preg_match('/^([a-zA-Z0-9_\.\-]+)\@(qq|163)\.com$/', $email)) {
            throw new \Exception('无效的邮箱地址', 10041);
        }
        if (!is_string($password) || !preg_match('/^[\@A-Za-z0-9\!\#\$\%\^\&\*\.\-\_\~]{5,15}$/', $password)) {
            throw new \Exception('无效的密码', 10042);
        }

        // 密码
        $password = md5($password);

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

        // 设置登录用户的 session
        $_SESSION['user'] = $user;

        return $user['id'];
    }
}
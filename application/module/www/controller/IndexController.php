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
        // view
    }

    /**
     * 显示验证码
     *
     * @param Request $requestInstance
     * @param Response $responseInstance
     */
    public function captchaAction(Request $requestInstance, Response $responseInstance)
    {
        // 生成二维码
        vendor('Captcha-master/autoload.php');
        $captcha = new Gregwar\Captcha\CaptchaBuilder();
        $captcha->build();

        // session
        $_SESSION['captcha'] = $captcha->getPhrase();

        return $this->image($responseInstance, $captcha->get());
    }
}
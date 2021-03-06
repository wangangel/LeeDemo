<?php
/**
 * ControllerAbstract.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/6 11:08
 */

abstract class ControllerAbstract
{
    /**
     * json
     *
     * @param Response $responseInstance
     * @param mixed $data
     * @return array
     */
    public function json($responseInstance, $data)
    {
        // 关闭视图
        Application::getInstance()->disableView();

        // 设置 json 响应头
        $responseInstance->setHeader('Content-Type', 'application/json;charset=UTF-8', true, 200);

        // json 格式（成功状态）
        return json_encode([
            'status' => true,
            'data' => $data
        ]);
    }

    /**
     * image
     *
     * @param Response $responseInstance
     * @param string $data
     * @return string
     */
    public function image($responseInstance, $data)
    {
        // 关闭视图
        Application::getInstance()->disableView();

        // 设置 image 响应头
        $responseInstance->setHeader('Content-Type', 'image/jpeg', true, 200);

        return $data;
    }

    /**
     * 重定向
     *
     * @param Response $responseInstance
     * @param string $url
     * @return bool
     */
    public function redirect($responseInstance, $url)
    {
        // 关闭视图
        Application::getInstance()->disableView();

        // 设置重定向响应头
        $responseInstance->setHeader('Location', $url);

        return true;
    }

    /**
     * 验证码校验
     *
     * @param string $captcha
     * @return bool
     */
    public function captchaCheck($captcha)
    {
        $check = !empty($_SESSION['captcha']) && is_string($captcha) && $_SESSION['captcha'] === strtolower($captcha);

        // 每次校验后，session captcha 都必须销毁，防止校验成功后，不刷新验证码而一直使用这个有效的验证码来绕过校验
        $_SESSION['captcha'] = null;

        return $check;
    }
}
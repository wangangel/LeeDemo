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
     * @param mixed $data
     * @return array
     */
    public function json($data)
    {
        // 关闭视图
        Application::getInstance()->disableView();

        // 设置 json 响应头
        Application::getInstance()->getResponseInstance()->setHeader('Content-Type', 'application/json;charset=UTF-8', true, 200);

        // json 格式（成功状态）
        return json_encode([
            'status' => true,
            'code' => '',
            'data' => $data
        ]);
    }

    /**
     * image
     *
     * @param string $data
     * @return string
     */
    public function image($data)
    {
        // 关闭视图
        Application::getInstance()->disableView();

        // 设置 image 响应头
        Application::getInstance()->getResponseInstance()->setHeader('Content-Type', 'image/jpeg', true, 200);

        return $data;
    }

    /**
     * 重定向
     *
     * @param string $url
     * @return bool
     */
    public function redirect($url)
    {
        // 关闭视图
        Application::getInstance()->disableView();

        // 设置重定向响应头
        Application::getInstance()->getResponseInstance()->setRedirect($url);

        return true;
    }
}
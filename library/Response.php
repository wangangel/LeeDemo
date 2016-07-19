<?php
/**
 * Response.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/8 16:27
 */

final class Response
{
    /**
     * @var string 响应的内容
     */
    private $_body = null;

    /**
     * 设置响应的内容
     *
     * @param string $content
     */
    public function setBody($content)
    {
        $this->_body = $content;
    }

    /**
     * 获取响应的内容
     *
     * @return string
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * 执行响应
     */
    public function response()
    {
        echo $this->getBody();
    }
}
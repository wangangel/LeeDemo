<?php
/**
 * HttpException.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/20 16:58
 */

class HttpException extends ExceptionAbstract
{
    /**
     * @var int 状态码
     */
    private $_statusCode = null;

    /**
     * 构造器
     *
     * @param int $statusCode
     * @param string $message
     * @param int $code
     */
    public function __construct($statusCode, $message, $code = 0)
    {
        $this->_statusCode = $statusCode;
        parent::__construct($message, $code);
    }

    /**
     * 获取状态码
     *
     * @return string
     */
    public function getStatusCode()
    {
        return $this->_statusCode;
    }
}
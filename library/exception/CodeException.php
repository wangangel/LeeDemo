<?php
/**
 * CodeException.php
 *
 * 由不恰当的编码导致的异常
 *
 * User: 670554666@qq.com
 * Date: 2016/7/6 16:03
 */

namespace library\exception;

class CodeException extends ExceptionAbstract
{
    /**
     * __construct
     *
     * @param string $message
     * @param int $code
     */
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }
}
<?php
/**
 * DatabaseException.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/18 9:24
 */

namespace library\exception;

class DatabaseException extends ExceptionAbstract
{
    /**
     * 构造器
     *
     * @param string $message
     * @param int $code
     */
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }
}
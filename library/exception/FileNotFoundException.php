<?php
/**
 * FileNotFoundException.php
 *
 * 文件不存在的异常
 *
 * User: 670554666@qq.com
 * Date: 2016/7/6 15:13
 */

class FileNotFoundException extends ExceptionAbstract
{
    /**
     * @var string 错误的文件路径
     */
    protected $_filePath = null;

    /**
     * 构造器
     *
     * @param string $filePath
     * @param string $message
     * @param int $code
     */
    public function __construct($filePath, $message, $code = 0)
    {
        $this->_filePath = $filePath;
        parent::__construct($message, $code);
    }

    /**
     * 获取错误的文件地址
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->_filePath;
    }
}
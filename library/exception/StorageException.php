<?php
/**
 * StorageException.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/18 9:24
 */

class StorageException extends \Exception
{
    /**
     * @var string 名称，如：mysqli / memcached
     */
    private $_name = null;

    /**
     * 构造器
     *
     * @param string $name
     * @param string $message
     * @param int $code
     */
    public function __construct($name, $message, $code = 0)
    {
        $this->_name = $name;
        parent::__construct($message, $code);
    }

    /**
     * 获取名称
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
}
<?php
/**
 * UndefinedException.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/19 17:09
 */

class UndefinedException extends \Exception
{
    /**
     * @var string 类型，包括：class / function / param / config
     */
    private $_type = null;

    /**
     * @var string 名称
     */
    private $_name = null;

    /**
     * 构造器
     *
     * @param string $type
     * @param string $name
     * @param string $message
     * @param int $code
     */
    public function __construct($type, $name, $message, $code = 0)
    {
        $this->_type = $type;
        $this->_name = $name;
        parent::__construct($message, $code);
    }

    /**
     * 获取类型
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
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
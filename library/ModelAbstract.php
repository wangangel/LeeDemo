<?php
/**
 * ModelAbstract.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/6 11:09
 */

namespace library;

use library\database\DatabaseFactory;

abstract class ModelAbstract
{
    /**
     * @var database\DatabaseInterface|null 当前数据库连接
     */
    protected $_db = null;

    /**
     * 构造器
     */
    public function __construct()
    {
        $this->_db = DatabaseFactory::getDriverInstance();
    }
}
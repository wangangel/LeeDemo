<?php
/**
 * ModelAbstract.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/6 11:09
 */

abstract class ModelAbstract
{
    /**
     * @var DatabaseInterface|null 数据库驱动对象
     */
    protected $_databaseInstance = null;

    /**
     * @var string 当前模型对应的表名
     */
    protected $_tableName = '';

    /**
     * 构造器
     */
    public function __construct()
    {
        $this->_databaseInstance = Application::getInstance()->getDatabaseInstance();
    }
}
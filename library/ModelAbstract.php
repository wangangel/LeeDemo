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
     * @var string 主键
     */
    protected $_pk = 'id';

    /**
     * 构造器
     */
    public function __construct()
    {
        $this->_databaseInstance = Application::getInstance()->getDatabaseInstance();
    }

    /**
     * 根据主键获取一条结果
     *
     * @return mixed
     */
    public function getByPK($value)
    {
        $ret = $this->_databaseInstance
            ->table($this->_tableName)
            ->where($this->_pk, 'eq', $value)
            ->limit(1)
            ->select();

        return $ret !== false && !empty($ret) ? $ret[0] : $ret;
    }

    /**
     * 添加一条记录
     *
     * @param array $data
     * @return mixed
     */
    public function addOne($data)
    {
        return $this->_databaseInstance
            ->table($this->_tableName)
            ->insert($data);
    }
}
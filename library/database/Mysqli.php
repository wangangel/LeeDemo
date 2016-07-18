<?php
/**
 * Mysqli.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/18 9:24
 */

namespace library\database;

use library\Application;
use library\exception\DatabaseException;

final class Mysqli implements DatabaseInterface
{
    /**
     * @var array 连接句柄数组
     */
    private $_connectArray = array();

    /**
     * 获取数据库连接
     *
     * @param bool $isMaster
     * @return resource
     * @throws DatabaseException
     */
    public function getConnect($isMaster)
    {
        $config = Application::getInstance()->getConfig('db');

        if ($isMaster) {
            $config = $config['master'];
        } else {
            $config = $config['slave'][mt_rand(0, 1)];
        }

        // 一种配置对应一个连接
        $key = md5(implode('', $config));

        if (!isset($this->_connectArray[$key])) {
            $connect = new \Mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);
            $connect->query('SET NAMES ' . $config['charset']);
            if (mysqli_connect_errno()) {
                throw new DatabaseException(mysqli_connect_error());
            }
            $this->_connectArray[$key] = $connect;
        }

        return $this->_connectArray[$key];
    }

    /**
     * 查询
     *
     * @param string $sql
     * @return array
     * @throws DatabaseException
     */
    public function query($sql)
    {
        $query = $this->getConnect(false)->query($sql);
        if ($query === false) {
            // todo: log
            throw new DatabaseException('mysqli query failed: ' . $sql);
        }

        $result = array();
        if ($query->num_rows > 0) {
            for ($i = 0; $i < $query->num_rows; $i++) {
                $result[$i] = $query->fetch_assoc();
            }
        }

        return $result;
    }

    public function execute($sql)
    {

    }

    public function field()
    {}

    public function table($tableName)
    {}

    public function join($way, $table, $leftField, $rightField)
    {}

    public function where()
    {}

    public function order($param)
    {}

    public function limit()
    {}

    public function select()
    {}

    public function insert($data)
    {}

    public function update($data)
    {}

    public function delete()
    {}

    public function startTrans()
    {}

    public function rollback()
    {}

    public function commit()
    {}
}
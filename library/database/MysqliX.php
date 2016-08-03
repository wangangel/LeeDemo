<?php
/**
 * MysqliX.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/18 9:24
 */

final class MysqliX implements DatabaseInterface
{
    /**
     * @var array 连接句柄数组
     */
    private $_connectArray = [];

    /**
     * @var array 用来拼凑 sql 的数据
     */
    private $_data = [];

    /**
     * 获取数据库连接
     *
     * @param bool $isMaster
     * @return resource
     * @throws Exception
     */
    public function getConnect($isMaster)
    {
        $database = Application::getInstance()->getConfigInstance()->get('database');

        // 选取配置
        $config = $isMaster ? $database['master'] : $database['slaves'][mt_rand(0, count($database['slaves']) - 1)];

        // 一种配置对应一个连接
        $key = md5(implode('', $config));

        // connect
        if (!isset($this->_connectArray[$key])) {
            $connect = new \Mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);
            $connect->query('SET NAMES ' . $config['charset']);
            if (mysqli_connect_errno()) {
                throw new \Exception(mysqli_connect_error(), 10012);
            }
            $this->_connectArray[$key] = $connect;
        }

        return $this->_connectArray[$key];
    }

    /**
     * 查询
     *
     * @param string $sql
     * @return mixed
     */
    public function query($sql)
    {
        $query = $this->getConnect(false)->query($sql);
        if ($query === false) {
            // todo: log
            return false;
        }

        $result = [];
        if ($query->num_rows > 0) {
            for ($i = 0; $i < $query->num_rows; $i++) {
                $result[] = $query->fetch_assoc();
            }
        }

        return $result;
    }

    /**
     * 执行
     *
     * 失败时返回 FALSE，通过 mysqli_query() 成功执行 SELECT, SHOW, DESCRIBE 或 EXPLAIN 查询会返回一个 mysqli_result 对象，其他查询则返回 TRUE
     *
     * @param string $sql
     * @return mixed
     */
    public function execute($sql)
    {
        $connect = $this->getConnect(true);

        $query = $connect->query($sql);
        if ($query === false) {
            // todo: log
            return false;
        }

        if (strpos($sql, 'INSERT') === 0) {
            return $connect->insert_id;
        } elseif(strpos($sql, 'UPDATE') === 0) {
            return $connect->affected_rows > 0;
        } else {
            return true;
        }
    }

    /**
     * 连贯操作：字段
     *
     * @return Mysqli
     */
    public function field()
    {
        $args = func_get_args();
        $argNum = func_num_args();

        $field = null;
        if ($argNum === 0) {
            $field = '*';
        } else {
            $array = [];
            foreach ($args as $arg) {
                if (is_string($arg)) {
                    $array[] = $arg;
                } elseif (is_array($arg)) {
                    $array[] = array_keys($arg)[0] . ' AS ' . $arg[array_keys($arg)[0]];
                }
            }
            $field = implode(', ', $array);
        }
        $this->_data['field'] = $field;

        return $this;
    }

    /**
     * 连贯操作：表名
     *
     * @param mixed $tableName
     * @return Mysqli
     */
    public function table($tableName)
    {
        $table = null;
        if (is_string($tableName)) {
            $table = $tableName;
        } elseif (is_array($tableName)) {
            $table = array_keys($tableName)[0] . ' AS ' . $tableName[array_keys($tableName)[0]];
        }
        $this->_data['table'] = $table;

        return $this;
    }

    /**
     * 连贯操作：JOIN
     *
     * @param string $way
     * @param mixed $tableName
     * @param string $leftField
     * @param string $rightField
     * @return Mysqli
     */
    public function join($way, $tableName, $leftField, $rightField)
    {
        $table = null;
        if (is_string($tableName)) {
            $table = $tableName;
        } elseif (is_array($tableName)) {
            $table = array_keys($tableName)[0] . ' AS ' . $tableName[array_keys($tableName)[0]];
        }

        $join = ' ' . strtoupper($way) . ' JOIN ' . $table . ' ON ' . $leftField . ' = ' . $rightField;
        if (isset($this->_data['join'])) {
            $this->_data['join'] .= $join;
        } else {
            $this->_data['join'] = $join;
        }

        return $this;
    }

    /**
     * 连贯操作：WHERE
     *
     * @return Mysqli
     */
    public function where()
    {
        $args = func_get_args();
        $argNum = func_num_args();

        $where = null;
        if ($argNum > 0) {
            if ($argNum === 1) {
                $linkWord = array_keys($args[0])[0];
                $array = [];
                foreach ($args[0][$linkWord] as $k => $v) {
                    if (is_string($k)) {
                        $arrayInner = array();
                        foreach ($v as $item) {
                            $arrayInner[] = '(' . $this->_parseWhere($item[0], $item[1], $item[2]) . ')';
                        }
                        $array[] = '(' . implode(' ' . strtoupper($k) . ' ', $arrayInner) . ')';
                    } else {
                        $array[] = '(' . $this->_parseWhere($v[0], $v[1], $v[2]) . ')';
                    }
                }
                $where = implode(' ' . strtoupper($linkWord) . ' ', $array);
            } elseif ($argNum === 3) {
                $where = $this->_parseWhere($args[0], $args[1], $args[2]);
            }
        }
        $this->_data['where'] = ' WHERE ' . $where;

        return $this;
    }

    /**
     * 辅助解析 WHERE
     *
     * @param string $field
     * @param string $condition
     * @param mixed $value
     * @return string
     */
    private function _parseWhere($field, $condition, $value)
    {
        $condition = str_replace(
            ['eq', 'neq', 'lk', 'nlk', 'bt', 'nbt', 'in', 'nin'],
            ['=', '<>', 'LIKE', 'NOT LIKE', 'BETWEEN', 'NOT BETWEEN', 'IN', 'NOT IN'],
            $condition
        );

        if (is_array($value)) {
            $value = '(' . implode(',', $value) . ')';
        } elseif (is_string($value)) {
            $value = '"' . $value . '"';
        }

        return $field . ' ' . $condition . ' ' . $value;
    }

    /**
     * 连贯操作：ORDER BY
     *
     * @return Mysqli
     */
    public function order()
    {
        $args = func_get_args();
        $argNum = func_num_args();

        $order = null;
        if ($argNum > 0) {
            if ($argNum === 1) {
                $order = $args[0] . ' ASC';
            } else {
                if ($argNum === 2 && is_string($args[0]) && is_string($args[1])) {
                    $order = $args[0] . ' ' . strtoupper($args[1]);
                } else {
                    $array = [];
                    foreach ($args as $arg) {
                        $array[] = $arg[0] . ' ' . strtoupper($arg[1]);
                    }
                    $order = implode(', ', $array);
                }
            }
        }
        $this->_data['order'] = ' ORDER BY ' . $order;

        return $this;
    }

    /**
     * 连贯操作：LIMIT
     *
     * @return Mysqli
     */
    public function limit()
    {
        $args = func_get_args();
        $argNum = func_num_args();

        $limit = null;
        if ($argNum > 0) {
            if ($argNum === 1) {
                $limit = $args[0];
            } elseif ($argNum === 2) {
                $limit = $args[0] . ', ' . $args[1];
            }
        }
        $this->_data['limit'] = ' LIMIT ' . $limit;

        return $this;
    }

    /**
     * SELECT
     *
     * @return mixed
     * @throws Exception
     */
    public function select()
    {
        $field = isset($this->_data['field']) ? $this->_data['field'] : '*';
        if (!isset($this->_data['table'])) {
            throw new \Exception('MysqliX', 10013);
        }
        $join = isset($this->_data['join']) ? $this->_data['join'] : '';
        $where = isset($this->_data['where']) ? $this->_data['where'] : '';
        $order = isset($this->_data['order']) ? $this->_data['order'] : '';
        $limit = isset($this->_data['limit']) ? $this->_data['limit'] : '';

        // mysql ELECT
        $sql = 'SELECT ' . $field . ' FROM ' . $this->_data['table'] . $join . $where . $order . $limit;

        return $this->query($sql);
    }

    /**
     * INSERT
     *
     * @param $data
     * @return mixed
     * @throws Exception
     */
    public function insert($data)
    {
        if (!isset($this->_data['table'])) {
            throw new \Exception('MysqliX', 10014);
        }

        $keys = implode(', ', array_keys($data));
        foreach ($data as $k => $v) {
            $data[$k] = is_string($v) ? '"' . $v . '"' : $v;
        }
        $value = implode(', ', $data);

        // mysql INSERT
        $sql = 'INSERT INTO ' . $this->_data['table'] . '(' . $keys . ') VALUES (' . $value . ')';

        return $this->execute($sql);
    }

    /**
     * UPDATE
     *
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function update($data)
    {
        if (!isset($this->_data['table'])) {
            throw new \Exception('MysqliX', 10015);
        }
        $where = isset($this->_data['where']) ? $this->_data['where'] : '';

        $array = [];
        foreach ($data as $k => $v) {
            if (strpos($v, '+') === 0 || strpos($v, '-') === 0) {
                $array[] = $k . ' = ' . $k . ' ' . $v;
            } else {
                $array[] = is_string($v) ? ($k . ' = "' . $v . '"') : ($k . ' = ' . $v);
            }
        }
        $set = implode(', ', $array);

        // mysql UPDATE
        $sql = 'UPDATE ' . $this->_data['table'] . ' SET ' . $set . $where;

        return $this->execute($sql);
    }

    public function delete()
    {}

    /**
     * 开启事务
     *
     * @return bool
     */
    public function startTrans()
    {
        $connect = $this->getConnect(true);

        $connect->autocommit(false);
        return $connect->begin_transaction();
    }

    /**
     * 回滚事务
     *
     * @return bool
     */
    public function rollback()
    {
        $connect = $this->getConnect(true);

        $rollback = $connect->rollback();
        $connect->autocommit(true);

        return $rollback;
    }

    /**
     * 提交事务
     *
     * @return bool
     */
    public function commit()
    {
        $connect = $this->getConnect(true);

        $commit = $connect->commit();
        $connect->autocommit(true);

        return $commit;
    }
}
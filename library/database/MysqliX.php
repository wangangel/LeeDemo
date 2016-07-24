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
     * @throws StorageException
     */
    public function getConnect($isMaster)
    {
        $database = Application::getInstance()->getConfigInstance()->get('database');

        if ($isMaster) {
            $config = $database['master'];
        } else {
            $config = $database['slaves'][mt_rand(0, count($database['slaves']) - 1)];
        }

        // 一种配置对应一个连接
        $key = md5(implode('', $config));

        if (!isset($this->_connectArray[$key])) {
            $connect = new \Mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);
            $connect->query('SET NAMES ' . $config['charset']);
            if (mysqli_connect_errno()) {
                throw new StorageException('mysqli', 'getConnect: ' . mysqli_connect_error());
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
     * @throws StorageException
     */
    public function query($sql)
    {
        $query = $this->getConnect(false)->query($sql);
        if ($query === false) {
            // todo: log
            throw new StorageException('mysqli', 'query: ' . $sql);
        }

        $result = [];
        if ($query->num_rows > 0) {
            for ($i = 0; $i < $query->num_rows; $i++) {
                $result[$i] = $query->fetch_assoc();
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
     * @throws StorageException
     */
    public function execute($sql)
    {
        $connect = $this->getConnect(true);

        $query = $connect->query($sql);
        if ($query === false) {
            // todo: log
            throw new StorageException('mysqli', 'execute: ' . $sql);
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
     * @throws StorageException
     */
    public function field()
    {
        $args = func_get_args();
        $num = func_num_args();

        $field = null;
        if ($num === 0) {
            $field = '*';
        } elseif ($num === 1) {
            if (!is_string($args[0])) {
                throw new StorageException('mysqli', 'field: 无效的参数');
            }
            $field = $args[0] === '*' ? '*' : $args[0];
        } else {
            $array = [];
            foreach ($args as $arg) {
                if (is_string($arg)) {
                    $array[] = $arg;
                } elseif (is_array($arg)) {
                    $array[] = array_keys($arg)[0] . ' AS ' . $arg[array_keys($arg)[0]];
                } else {
                    throw new StorageException('mysqli', 'field: 无效的参数');
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
     * @throws StorageException
     */
    public function table($tableName)
    {
        if (empty($tableName)) {
            throw new StorageException('mysqli', 'table: 缺少参数 -> $tableName');
        }

        $table = null;
        if (is_string($tableName)) {
            $table = $tableName;
        } elseif (is_array($tableName)) {
            $table = array_keys($tableName)[0] . ' AS ' . $tableName[array_keys($tableName)[0]];
        } else {
            throw new StorageException('mysqli', 'table: 无效的参数 -> $tableName');
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
     * @throws StorageException
     */
    public function join($way, $tableName, $leftField, $rightField)
    {
        $allowWays = ['inner', 'left', 'left outer', 'right', 'right outer', 'full', 'full outer'];
        if (!in_array(strtolower($way), $allowWays)) {
            throw new StorageException('mysqli', 'join: 无效的参数 -> $way');
        }

        $table = null;
        if (empty($tableName)) {
            throw new StorageException('mysqli', 'join: 缺少参数 -> $tableName');
        } else {
            if (is_string($tableName)) {
                $table = $tableName;
            } elseif (is_array($tableName)) {
                $table = array_keys($tableName)[0] . ' AS ' . $tableName[array_keys($tableName)[0]];
            } else {
                throw new StorageException('mysqli', 'join: 无效的参数 -> $tableName');
            }
        }

        if (empty($leftField)) {
            throw new StorageException('mysqli', 'join: 缺少参数 -> $leftField');
        } else {
            if (!is_string($leftField)) {
                throw new StorageException('mysqli', 'join: 无效的参数 -> $leftField');
            }
        }

        if (empty($rightField)) {
            throw new StorageException('mysqli', 'join: 缺少参数 -> $rightField');
        } else {
            if (!is_string($rightField)) {
                throw new StorageException('mysqli', 'join: 无效的参数 -> $rightField');
            }
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
     * @throws StorageException
     */
    public function where()
    {
        $args = func_get_args();
        $num = func_num_args();

        $where = null;
        if ($num > 0) {
            if ($num === 1) {
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
            } elseif ($num === 3) {
                $where = $this->_parseWhere($args[0], $args[1], $args[2]);
            } else {
                throw new StorageException('mysqli', 'where: 无效的参数');
            }
        } else {
            throw new StorageException('mysqli', 'where: 缺少参数');
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
     * @throws StorageException
     */
    private function _parseWhere($field, $condition, $value)
    {
        if (!is_string($field) || !is_string($condition)) {
            throw new StorageException('mysqli', 'where: 无效的参数');
        }

        $matches = ['eq', 'neq', 'lk', 'nlk', 'bt', 'nbt', 'in', 'nin'];
        if (!in_array($condition, $matches)) {
            throw new StorageException('mysqli', 'where: 无效的参数');
        }
        $condition = str_replace(
            $matches,
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
     * @throws StorageException
     */
    public function order()
    {
        $args = func_get_args();
        $num = func_num_args();

        $order = null;
        if ($num > 0) {
            if ($num === 1) {
                if (is_string($args[0])) {
                    $order = $args[0] . ' ASC';
                } else {
                    throw new StorageException('mysqli', 'order: 无效的参数');
                }
            } elseif ($num === 2) {
                if (is_string($args[0]) && is_string($args[1])) {
                    $order = $args[0] . ' ' . strtoupper($args[1]);
                } elseif (is_array($args[0]) && count($args[0]) === 2 && in_array(strtolower($args[0][1]), ['asc', 'desc']) && is_array($args[1]) && count($args[1]) === 2 && in_array(strtolower($args[1][1]), ['asc', 'desc'])) {
                    $order = $args[0][0] . ' ' . strtoupper($args[0][1]) . ', ' . $args[1][0] . ' ' . strtoupper($args[1][1]);
                } else {
                    throw new StorageException('mysqli', 'order: 无效的参数');
                }
            } else {
                $array = [];
                foreach ($args as $arg) {
                    if (!is_array($arg) || count($arg) !== 2 || !in_array(strtolower($arg[1]), ['asc', 'desc'])) {
                        throw new StorageException('mysqli', 'order: 无效的参数');
                    }
                    $array[] = $arg[0] . ' ' . strtoupper($arg[1]);
                }
                $order = implode(', ', $array);
            }
        } else {
            throw new StorageException('mysqli', 'order: 缺少参数');
        }
        $this->_data['order'] = ' ORDER BY ' . $order;

        return $this;
    }

    /**
     * 连贯操作：LIMIT
     *
     * @return Mysqli
     * @throws StorageException
     */
    public function limit()
    {
        $args = func_get_args();
        $num = func_num_args();

        $limit = null;
        if ($num > 0) {
            if ($num === 1) {
                $limit = $args[0];
            } elseif ($num === 2) {
                $limit = $args[0] . ', ' . $args[1];
            } else {
                throw new StorageException('mysqli', 'limit: 参数数量有误');
            }
        } else {
            throw new StorageException('mysqli', 'limit: 缺少参数');
        }
        $this->_data['limit'] = ' LIMIT ' . $limit;

        return $this;
    }

    /**
     * SELECT
     *
     * @return array
     * @throws StorageException
     */
    public function select()
    {
        $field = isset($this->_data['field']) ? $this->_data['field'] : '*';
        if (!isset($this->_data['table'])) {
            throw new StorageException('mysqli', 'select: 缺少表名');
        }
        $join = isset($this->_data['join']) ? $this->_data['join'] : '';
        $where = isset($this->_data['where']) ? $this->_data['where'] : '';
        $order = isset($this->_data['order']) ? $this->_data['order'] : '';
        $limit = isset($this->_data['limit']) ? $this->_data['limit'] : '';

        // mysqli 下的 SELECT 标准（未实现一些比如：UNION / GROUP BY / HAVING 等）
        $sql = 'SELECT ' . $field . ' FROM ' . $this->_data['table'] . $join . $where . $order . $limit;

        return $this->query($sql);
    }

    /**
     * INSERT
     *
     * @param $data
     * @return int
     * @throws StorageException
     */
    public function insert($data)
    {
        if (!isset($this->_data['table'])) {
            throw new StorageException('mysqli', 'inser: 缺少表名');
        }

        $keys = implode(', ', array_keys($data));
        foreach ($data as $k => $v) {
            $data[$k] = is_string($v) ? '"' . $v . '"' : $v;
        }
        $value = implode(', ', $data);

        $sql = 'INSERT INTO ' . $this->_data['table'] . '(' . $keys . ') VALUES (' . $value . ')';

        return $this->execute($sql);
    }

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
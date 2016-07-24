<?php
/**
 * DatabaseInterface.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/16 21:48
 */

interface DatabaseInterface
{
    /**
     * 获取数据库连接
     *
     * $isMaster = true 走主库，否则走从库，多个从库则每调用一次随机选取一个
     *
     * @param bool $isMaster
     * @return resource
     */
    public function getConnect($isMaster);

    /**
     * 查询
     *
     * 将 select 引导到这里，强制走从库
     *
     * 虽然不建议这么做，但是你也可以直接调用此方法来查询原生的 sql，主要是解决一些连贯操作无法实现的复杂 sql
     *
     * @param string $sql
     * @return array
     */
    public function query($sql);

    /**
     * 执行
     *
     * 将 update / insert / delete 引导到这里，强制走主库
     *
     * 虽然不建议这么做，但是你也可以直接调用此方法来执行原生的 sql，主要是解决一些连贯操作无法实现的复杂 sql
     *
     * @param string $sql
     * @return mixed
     */
    public function execute($sql);

    /**
     * 连贯操作：字段
     *
     * 只调用一次
     *
     * 不调用 = field() = field('*') / field('id', 'name') / field('id', ['COUNT(*)' => 'total'])
     *
     * @return DatabaseInterface
     */
    public function field();

    /**
     * 连贯操作：表名
     *
     * 只调用一次
     *
     * table('post') / table(['post' => 'p'])
     *
     * @param mixed $tableName
     * @return DatabaseInterface
     */
    public function table($tableName);

    /**
     * 连贯操作：JOIN
     *
     * 可多次调用
     *
     * join('left', 'user', 'post.user_id', 'user.id') / join('inner', ['user' => 'u'], 'p.user_id', 'u.id')
     *
     * @param string $way
     * @param mixed $tableName
     * @param string $leftField
     * @param string $rightField
     * @return DatabaseInterface
     */
    public function join($way, $tableName, $leftField, $rightField);

    /**
     * 连贯操作：WHERE
     *
     * 只调用一次
     *
     * where('id', 'eq', 1)
     *
     * where([
     *      'or' => [
     *          ['id' , 'eq', 1],
     *          'and' => [
     *              ['name', 'lk', '%xxx%'],
     *              ['score', 'gt', 100]
     *          ],
     *          ['time', 'lt', '1999-99-99']
     *      ]
     * ])
     * 相当于（最多2层，请不要再深入嵌套）
     * (id = 1) OR ((name like '%xxx%') AND (score > 100)) OR (time < '1999-99-99')
     *
     * eq:等于，neq:不等于，lk:LIKE，nlk:NOT LIKE，bt:BETWEEN，nbt:NOT BETWEEN，in:IN，nin:NOT IN
     *
     * @return DatabaseInterface
     */
    public function where();

    /**
     * 连贯操作：ORDER BY
     *
     * 只调用一次
     *
     * order('id') / order('id', 'desc') / order(['id', 'asc'], ['score', 'desc'])
     *
     * 请不要实现一些诡异的写法，比如：order('id', 'score', 'desc') / order('id', 'asc', ['score', 'desc'])，本身从理解上也存在误导
     *
     * @return DatabaseInterface
     */
    public function order();

    // public function group();

    // public function having();

    /**
     * 连贯操作：LIMIT
     *
     * 只调用一次
     *
     * limit(1) / limit(1, 10)
     *
     * @return DatabaseInterface
     */
    public function limit();

    /**
     * SELECT
     *
     * @return array
     */
    public function select();

    /**
     * INSERT
     *
     * insert(['name' => 'aaa', 'sex' => 2, 'age' => 22])
     *
     * @param $data
     * @return int
     */
    public function insert($data);

    /**
     * UPDATE
     *
     * update(['name' => 'aaa', 'sex' => 2, 'age' => 22])
     *
     * @param array $data
     * @return bool
     */
    public function update($data);

    /**
     * DELETE
     *
     * @return bool
     */
    public function delete();

    /**
     * 开启事务
     *
     * @return bool
     */
    public function startTrans();

    /**
     * 回滚事务
     *
     * @return bool
     */
    public function rollback();

    /**
     * 提交事务
     *
     * @return bool
     */
    public function commit();
}
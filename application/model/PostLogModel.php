<?php
/**
 * PostLogModel.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/27 16:25
 */

class PostLogModel extends ModelAbstract
{
    public function getOwnerByDays($days, $userId)
    {
        $ret = $this->_databaseInstance
            ->table('post_log')
            ->where([
                'and' => [
                    ['days', 'eq', $days],
                    ['user_id', 'eq', $userId]
                ]
            ])
            ->limit(1)
            ->select();

        if ($ret === false) {
            return '日志Log查询异常';
        }
    }
}
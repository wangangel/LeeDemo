<?php
/**
 * PostLogModel.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/27 16:25
 */

class PostLogModel extends ModelAbstract
{
    /**
     * 根据 days 和 user_id 获取日志记录
     *
     * @param string $days
     * @param int $userId
     * @return mixed
     */
    public function getOwnerByDays($days, $userId)
    {
        $postLog = $this->_databaseInstance
            ->table('post_log')
            ->where([
                'and' => [
                    ['days', 'eq', $days],
                    ['user_id', 'eq', $userId]
                ]
            ])
            ->limit(1)
            ->select();

        return $postLog !== false && !empty($postLog) ? $postLog[0] : $postLog;
    }

    /**
     * 插入一条记录
     *
     * @param string $days
     * @param int $userId
     * @return mixed
     */
    public function addOne($days, $userId)
    {
        return $this->_databaseInstance
            ->table('post_log')
            ->insert([
                'days' => $days,
                'user_id' => $userId
            ]);
    }

    /**
     * 更新指定日期的日志计数
     *
     * @param int $logId
     * @param int $count
     * @return bool
     */
    public function updatePostCount($logId, $count = 0)
    {
        $update = null;
        if ($count === 0) {
            return false;
        } elseif ($count > 0) {
            $update = '+ ' . $count;
        } else {
            $update = '- ' . abs($count);
        }

        return $this->_databaseInstance
            ->table('post_log')
            ->where('id', 'eq', $logId)
            ->update([
                'count_post' => $update
            ]);
    }
}
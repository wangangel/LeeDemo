<?php
/**
 * PostBodyModel.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/20 17:12
 */

class PostBodyModel extends ModelAbstract
{
    /**
     * 根据 post_id 获取文章正文
     *
     * @param int $postId
     * @return mixed
     */
    public function getByPostId($postId)
    {
        $ret = $this->_databaseInstance
            ->table('post_body')
            ->where('post_id', 'eq', $postId)
            ->limit(1)
            ->select();

        if ($ret === false) {
            return '日志正文查询异常';
        }
        if (empty($ret)) {
            return '日志正文丢失';
        }

        return $ret[0];
    }

    /**
     * 插入一条记录
     *
     * @param int $postId
     * @param string $body
     * @return int
     */
    public function addOne($postId, $body)
    {
        return $this->_databaseInstance
            ->table('post_body')
            ->insert([
                'post_id' => $postId,
                'body' => $body
            ]);
    }
}
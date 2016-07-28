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
        $postBody = $this->_databaseInstance
            ->table('post_body')
            ->where('post_id', 'eq', $postId)
            ->limit(1)
            ->select();

        return $postBody !== false && !empty($postBody) ? $postBody[0] : $postBody;
    }

    /**
     * 插入一条记录
     *
     * @param int $postId
     * @param string $body
     * @return mixed
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
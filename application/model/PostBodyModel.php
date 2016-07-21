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
     * @return array
     */
    public function getByPostId($postId)
    {
        return $this->_db
            ->table('post_body')
            ->where('post_id', 'eq', $postId)
            ->limit(1)
            ->select();
    }
}
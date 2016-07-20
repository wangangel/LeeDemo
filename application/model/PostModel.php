<?php
/**
 * PostModel.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/20 11:18
 */

class PostModel extends ModelAbstract
{
    /**
     * 根据 ID 获取文章
     *
     * @param int $postId
     * @return array
     */
    public function getById($postId)
    {
        return $this->_db->table('post')->where('id', 'eq', $postId)->limit(1)->select();
    }


    public function getPagedList()
    {
        $list = $this->_db->table('post')->order('id', 'desc')->limit(0, 10)->select();

        $page = '';

        return [
            'list' => $list,
            'page' => $page
        ];
    }
}
<?php
/**
 * PostModel.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/20 11:18
 */

class PostModel extends ModelAbstract
{
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
<?php
/**
 * UserModel.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/18 10:27
 */

class UserModel extends ModelAbstract
{
    public function test()
    {
        $ret = $this->_db
            ->field('id', ['email' => 'a'])
            ->table(['user' => 'u'])
            ->join('left outer', ['email' => 'a'], 'user.id', 'post.user_id')
            ->join('inner', ['xxx' => 'a'], 'category.id', 'post.category_id')
            ->where([
                'or' => [
                    ['name', 'lk', 'aaa'],
                    'and' => [
                        ['name', 'lk', 'aaa'],
                        ['name', 'lk', 'aaa']
                    ],
                    ['name', 'lk', 'aaa']
                ]
            ])
            ->order(['id', 'desc'], ['time', 'desc'], ['xxx', 'desc'])
            ->limit(1, 12)
            ->select();

        var_dump($ret);
    }
}
<?php
/**
 * UserModel.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/18 10:27
 */

namespace application\model;

use library\ModelAbstract;

class UserModel extends ModelAbstract
{
    public function getCurrent()
    {

    }

    public function test()
    {
        return $this->_db->query('SELECT * FROM user');
    }
}
<?php
/**
 * UserModel.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/18 10:27
 */

class UserModel extends ModelAbstract
{
    /**
     * 用户状态
     */
    const STATUS_VERIFY = 1;    // 待审核
    const STATUS_NORMAL = 2;    // 正常
    const STATUS_DELETE = 3;    // 已删除

    /**
     * @var string 表名
     */
    protected $_tableName = 'user';

    /**
     * 根据 email 获取用户
     *
     * @param string $email
     * @return mixed
     */
    public function getByEmail($email)
    {
        $user = $this->_databaseInstance
            ->table($this->_tableName)
            ->where('email', 'eq', $email)
            ->limit(1)
            ->select();

        return $user !== false && !empty($user) ? $user[0] : $user;
    }

    /**
     * 根据 nickname 获取用户
     *
     * @param string $nickname
     * @return mixed
     */
    public function getByNickname($nickname)
    {
        $user = $this->_databaseInstance
            ->table($this->_tableName)
            ->where('nickname', 'eq', $nickname)
            ->limit(1)
            ->select();

        return $user !== false && !empty($user) ? $user[0] : $user;
    }

    /**
     * 根据 email 和 password 获取用户
     *
     * @param string $email
     * @param string $password
     * @return mixed
     */
    public function getByEmailAndPassword($email, $password)
    {
        $user = $this->_databaseInstance
            ->table($this->_tableName)
            ->where([
                'and' => [
                    ['email', 'eq', $email],
                    ['password', 'eq', $password]
                ]
            ])
            ->limit(1)
            ->select();

        return $user !== false && !empty($user) ? $user[0] : $user;
    }

    /**
     * 更新正常状态的日志计数
     *
     * @param int $userId
     * @param int $countNormal
     * @return bool
     */
    public function updateNormalPostCount($userId, $countNormal = 0)
    {
        $update = null;
        if ($countNormal === 0) {
            return false;
        } elseif ($countNormal > 0) {
            $update = '+ ' . $countNormal;
        } else {
            $update = '- ' . abs($countNormal);
        }

        return $this->_databaseInstance
            ->table($this->_tableName)
            ->where('id', 'eq', $userId)
            ->update([
                'count_normal_post' => $update
            ]);
    }
}
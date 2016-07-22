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
     * 根据 id 获取用户
     *
     * @param int $userId
     * @param bool $statusCheck
     * @return mixed
     */
    public function getById($userId, $statusCheck = false)
    {
        $ret = $this->_databaseInstance
            ->table('user')
            ->where('id', 'eq', $userId)
            ->limit(1)
            ->select();

        if (empty($ret)) {
            return '用户不存在';
        }

        $user = $ret[0];
        if ($statusCheck) {
            $status = intval($user['status']);
            if ($status === self::STATUS_VERIFY) {
                return '用户正在审核中';
            } elseif ($status === self::STATUS_DELETE) {
                return '用户已被删除';
            } elseif ($status !== self::STATUS_NORMAL) {
                return '用户状态异常';
            }
        }

        return $user;
    }
}
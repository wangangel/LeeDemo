<?php
/**
 * PostCategoryModel.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/21 9:27
 */

class PostCategoryModel extends ModelAbstract
{
    /**
     * 分类状态
     */
    const STATUS_VERIFY = 1;    // 待审核
    const STATUS_NORMAL = 2;    // 正常
    const STATUS_DELETE = 3;    // 已删除

    /**
     * 获取待审核状态的值
     *
     * @return int
     */
    public function getStatusVerify()
    {
        return self::STATUS_VERIFY;
    }

    /**
     * 获取正常状态的值
     *
     * @return int
     */
    public function getStatusNormal()
    {
        return self::STATUS_NORMAL;
    }

    /**
     * 获取已删除状态的值
     *
     * @return int
     */
    public function getStatusDelete()
    {
        return self::STATUS_DELETE;
    }

    /**
     * 根据 id 和 user_id 获取分类
     *
     * @param int $categoryId
     * @param int $userId
     * @return array
     */
    public function getOwnerById($categoryId, $userId)
    {
        return $this->_databaseInstance
            ->table('post_category')
            ->where([
                'and' => [
                    ['id', 'eq', $categoryId],
                    ['user_id', 'eq', $userId]
                ]
            ])
            ->limit(1)
            ->select();
    }

    /**
     * 根据 user_id 获取所有正常状态的文章分类
     *
     * @param int $userId
     * @return array
     */
    public function getNormalListByUserId($userId)
    {
        return $this->_databaseInstance
            ->table('post_category')
            ->where([
                'and' => [
                    ['user_id', 'eq', $userId],
                    ['status', 'eq', self::STATUS_NORMAL]
                ]
            ])
            ->order('id', 'asc')
            ->select();
    }
}
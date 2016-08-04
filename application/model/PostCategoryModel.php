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
     * @var string 表名
     */
    protected $_tableName = 'post_category';

    /**
     * 根据 id 和 user_id 获取分类
     *
     * @param int $categoryId
     * @param int $userId
     * @return mixed
     */
    public function getOwnerById($categoryId, $userId)
    {
        $postCategory = $this->_databaseInstance
            ->table($this->_tableName)
            ->where([
                'and' => [
                    ['id', 'eq', $categoryId],
                    ['user_id', 'eq', $userId]
                ]
            ])
            ->limit(1)
            ->select();

        return $postCategory !== false && !empty($postCategory) ? $postCategory[0] : $postCategory;
    }

    /**
     * 根据 user_id 获取所有正常状态的分类
     *
     * @param int $userId
     * @return mixed
     */
    public function getNormalListByUserId($userId)
    {
        return $this->_databaseInstance
            ->table($this->_tableName)
            ->where([
                'and' => [
                    ['user_id', 'eq', $userId],
                    ['status', 'eq', self::STATUS_NORMAL]
                ]
            ])
            ->order('id', 'asc')
            ->select();
    }

    /**
     * 更新正常状态的日志计数
     *
     * @param int $categoryId
     * @param int $countNormal
     * @return bool
     */
    public function updateNormalPostCount($categoryId, $countNormal = 0)
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
            ->where('id', 'eq', $categoryId)
            ->update([
                'count_normal_post' => $update
            ]);
    }
}
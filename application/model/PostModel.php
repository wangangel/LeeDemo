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
     * 文章状态
     */
    const STATUS_VERIFY = 1;    // 待审核
    const STATUS_NORMAL = 2;    // 正常
    const STATUS_DELETE = 3;    // 已删除

    /**
     * 根据 id 和 user_id 获取日志
     *
     * @param int $postId
     * @param int $userId
     * @param bool $statusCheck
     * @return array
     */
    public function getOwnerById($postId, $userId, $statusCheck = false)
    {
        $ret = $this->_databaseInstance
            ->table('post')
            ->where([
                'and' => [
                    ['id', 'eq', $postId],
                    ['user_id', 'eq', $userId]
                ]
            ])
            ->limit(1)
            ->select();

        if (empty($ret)) {
            return '日志不存在';
        }

        $post = $ret[0];
        if ($statusCheck) {
            $status = intval($post['status']);
            if ($status === self::STATUS_VERIFY) {
                return '日志正在审核中';
            } elseif ($status === self::STATUS_DELETE) {
                return '日志已被删除';
            } elseif ($status !== self::STATUS_NORMAL) {
                return '日志状态异常';
            }
        }

        return $post;
    }

    /**
     * 获取带分页的日志列表
     *
     * @param int $page
     * @param int $userId
     * @param int $categoryId
     * @param int $status
     * @return array
     */
    public function getPagedList($page, $userId = 0, $categoryId = 0, $status = 0)
    {
        $where = [];
        if ($userId !== 0) {
            $where[] = ['user_id', 'eq', $userId];
        }
        if ($categoryId !== 0) {
            $where[] = ['category_id', 'eq', $categoryId];
        }
        if ($status !== 0) {
            $where[] = ['status', 'eq', $status];
        }
        if (!empty($where)) {
            $where = ['and' => $where];
        }

        $per = Application::getInstance()->getConfigInstance()->get('display.postNumPerPage');
        if (empty($where)) {
            $list = $this->_databaseInstance
                ->table('post')
                ->order('id', 'desc')
                ->limit(($page - 1) * $per, $per)
                ->select();
        } else {
            $list = $this->_databaseInstance
                ->table('post')
                ->where($where)
                ->order('id', 'desc')
                ->limit(($page - 1) * $per, $per)
                ->select();
        }

        $pagenavi = '';

        return [
            'list' => $list,
            'pagenavi' => $pagenavi
        ];
    }

    /**
     * 添加一条记录
     *
     * @param array $data
     * @return int
     */
    public function addOne($data)
    {
        return $this->_databaseInstance->table('post')->insert($data);
    }
}
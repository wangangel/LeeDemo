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
     * 日志状态
     */
    const STATUS_VERIFY = 1;    // 待审核
    const STATUS_NORMAL = 2;    // 正常
    const STATUS_RECYCLE = 3;   // 回收站
    const STATUS_DELETE = 4;    // 已删除

    /**
     * 根据 id 和 user_id 获取日志
     *
     * @param int $postId
     * @param int $userId
     * @return mixed
     */
    public function getOwnerById($postId, $userId)
    {
        $post = $this->_databaseInstance
            ->table('post')
            ->where([
                'and' => [
                    ['id', 'eq', $postId],
                    ['user_id', 'eq', $userId]
                ]
            ])
            ->limit(1)
            ->select();

        return $post !== false && !empty($post) ? $post[0] : $post;
    }

    /**
     * 获取带分页的日志列表
     *
     * @param int $page
     * @param int $userId
     * @param int $categoryId
     * @param int $status
     * @return mixed
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
        if ($list === false) {
            return false;
        }

        $pagenavi = '';

        return [
            'list' => $list,
            'pagenavi' => $pagenavi
        ];
    }

    /**
     * 插入一条记录
     *
     * @param int $categoryId
     * @param int $userId
     * @param string $title
     * @param string $description
     * @return mixed
     */
    public function addOne($categoryId, $userId, $title, $description)
    {
        return $this->_databaseInstance
            ->table('post')
            ->insert([
                'category_id' => $categoryId,
                'user_id' => $userId,
                'title' => $title,
                'description' => $description,
                'status' => 2,
                'time_add' => time()
            ]);
    }

    /**
     * 发布一篇日志
     *
     * 涉及到 post / post_body / post_category
     *
     * @param int $categoryId
     * @param int $userId
     * @param string $title
     * @param string $body
     * @return mixed
     */
    public function publish($categoryId, $userId, $title, $body)
    {
        // 开启事务
        $this->_databaseInstance->startTrans();

        // todo: 敏感词检查

        // 插入 post
        $postId = $this->addOne($categoryId, $userId, $title, substr($body, 0, 250));
        if ($postId === false) {
            return false;
        }

        // 插入 post_body
        $postBodyId = M('postBody')->addOne($postId, $body);
        if ($postBodyId === false) {
            $this->_databaseInstance->rollback();
            return false;
        }

        // 更新 post_category 正常日志计数
        $updateCategoryCount = M('postCategory')->updateNormalPostCount($categoryId, 1);
        if ($updateCategoryCount === false) {
            $this->_databaseInstance->rollback();
            return false;
        }

        // 更新 user 正常日志计数
        $updateUserCount = M('user')->updateNormalPostCount($userId, 1);
        if ($updateUserCount === false) {
            $this->_databaseInstance->rollback();
            return false;
        }

        // 插入或更新 post_log 本日日志记录
        $days = date('Y-m-d');
        $postLog = M('postLog')->getOwnerByDays($days, $userId);
        if ($postLog === false) {
            $this->_databaseInstance->rollback();
            return false;
        }
        $postLogId = 0;
        if (empty($postLog)) {
            $postLogId = M('postLog')->addOne($days, $userId);
            if ($postLogId === false) {
                $this->_databaseInstance->rollback();
                return false;
            }
        } else {
            $postLogId = $postLog['id'];
        }
        $updateLogCount = M('postLog')->updatePostCount($postLogId, 1);
        if ($updateLogCount === false) {
            $this->_databaseInstance->rollback();
            return false;
        }

        // 提交事务
        $this->_databaseInstance->commit();

        return $postId;
    }
}
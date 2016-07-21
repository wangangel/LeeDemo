<?php
/**
 * BlogController.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/16 13:52
 */

class BlogController extends ControllerAbstract
{
    /**
     * @var array|null 当前博主
     */
    private $_user = null;

    /**
     * 构造器
     *
     * @throws HttpException
     */
    public function __construct()
    {
        $userId = I('get', 'userId', 0, 'intval');

        // 博主
        $user = M('user')->getById($userId);
        if (empty($user)) {
            throw new HttpException(404, '用户不存在');
        } else {
            $user = $user[0];
            $status = intval($user['status']);

            // 博主状态判断
            if ($status === 0) {
                throw new HttpException(404, '用户状态异常');
            } elseif ($status === M('user')->getStatusVerify()) {
                throw new HttpException(404, '用户正在审核中');
            } elseif ($status === M('user')->getStatusDelete()) {
                throw new HttpException(404, '用户已被删除');
            }
        }

        $this->_user = $user;
    }

    /**
     * 首页
     */
    public function indexAction()
    {
        M('user')->test();
    }

    /**
     * 日志列表
     *
     * @throws HttpException
     */
    public function postListAction()
    {
        $categoryId = I('get', 'categoryId', 0, 'intval');
        $page = I('get', 'page', 1, 'intval');

        // 分类有效性判断
        $category = [];
        if ($categoryId !== 0) {
            $category = M('postCategory')->getOwnerById($categoryId, $this->_user['id']);
            if (empty($category)) {
                throw new HttpException(404, '该分类不存在');
            } else {
                $category = $category[0];
                $status = intval($category['status']);

                // 分类状态判断
                if ($status === 0) {
                    throw new HttpException(404, '分类状态异常');
                } elseif ($status === M('postCategory')->getStatusVerify()) {
                    throw new HttpException(404, '分类正在审核中');
                } elseif ($status === M('postCategory')->getStatusDelete()) {
                    throw new HttpException(404, '分类已被删除');
                }
            }
        }

        // 日志列表
        $data = M('post')->getPagedList($page, $this->_user['id'], $categoryId, M('post')->getStatusNormal());

        // 日志分类
        $categories = M('postCategory')->getNormalListByUserId($this->_user['id']);

        return [
            'param' => [
              'categoryId' => $categoryId,
              'page' => $page
            ],
            'user' => $this->_user,
            'data' => $data,
            'categories' => $categories,
            'category' => $category
        ];
    }

    /**
     * 日志正文
     *
     * @throws HttpException
     */
    public function postAction()
    {
        $postId = I('get', 'postId', 0, 'intval');

        // 日志
        $post = M('post')->getOwnerById($postId, $this->_user['id']);
        if (empty($post)) {
            throw new HttpException(404, '该日志不存在');
        } else {
            $post = $post[0];
            $status = intval($post['status']);

            // 日志状态判断
            if ($status === 0) {
                throw new HttpException(404, '日志状态异常');
            } elseif ($status === 1) {
                throw new HttpException(404, '日志正在审核中');
            } elseif ($status === 3) {
                throw new HttpException(404, '日志已被删除');
            }
        }

        // 日志正文
        $postBody = M('postBody')->getByPostId($postId);
        if (empty($postBody)) {
            throw new HttpException(404, '日志正文丢失');
        }
        $post['body'] = $postBody[0]['body'];

        return [
            'user' => $this->_user,
            'post' => $post
        ];
    }
}
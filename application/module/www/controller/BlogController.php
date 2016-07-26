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
        $user = Application::getInstance()->getModelInstance('user')->getById($userId, true);
        if (is_string($user)) {
            throw new HttpException(404, $user);
        }

        $this->_user = $user;
    }

    /**
     * 首页
     */
    public function indexAction()
    {
        // todo
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

        // 当前分类
        $categoryModelInstance = Application::getInstance()->getModelInstance('postCategory');
        $category = [];
        if ($categoryId !== 0) {
            $category = $categoryModelInstance->getOwnerById($categoryId, $this->_user['id'], true);
            if (is_string($category)) {
                throw new HttpException(404, $category);
            }
        }

        // 日志列表
        $data = Application::getInstance()->getModelInstance('post')->getPagedList($page, $this->_user['id'], $categoryId, PostModel::STATUS_NORMAL);
        if ($data === false) {
            throw new HttpException(404, '日志列表获取失败');
        }

        // 分类列表
        $categoryList = $categoryModelInstance->getNormalListByUserId($this->_user['id']);
        if ($categoryList === false) {
            throw new HttpException(404, '分类列表获取失败');
        }

        return [
            'param' => [
              'categoryId' => $categoryId,
              'page' => $page
            ],
            'user' => $this->_user,
            'category' => $category,
            'data' => $data,
            'categoryList' => $categoryList
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
        $post = Application::getInstance()->getModelInstance('post')->getOwnerById($postId, $this->_user['id'], true);
        if (is_string($post)) {
            throw new HttpException(404, $post);
        }

        // 日志正文
        $postBody = Application::getInstance()->getModelInstance('postBody')->getByPostId($postId);
        if (is_string($postBody)) {
            throw new HttpException(404, $postBody);
        }
        $post['body'] = $postBody['body'];

        return [
            'user' => $this->_user,
            'post' => $post
        ];
    }
}
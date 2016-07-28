<?php
/**
 * PublishController.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/22 15:02
 */

class PublishController extends ControllerAbstract
{
    /**
     * @var array 当前登录的用户
     */
    private $_user = null;

    /**
     * 构造器
     *
     * @throws HttpException
     */
    public function __construct()
    {
        if (isset($_SESSION['user'])) {
            $this->_user = $_SESSION['user'];
        } else {
            throw new HttpException(404, '您尚未登录');
        }
    }

    /**
     * 发布日志
     */
    public function postAddAction()
    {
        // 分类列表
        $categories = M('postCategory')->getNormalListByUserId($this->_user['id']);

        return [
            'user' => $this->_user,
            'categories' => $categories
        ];
    }

    /**
     * 发布日志 - 提交
     */
    public function postAddSubmitAction()
    {
        Application::getInstance()->disableView();

        $title = I('post', 'title', '', 'trim');
        $categoryId = I('post', 'categoryId', 0, 'intval');
        $body = I('post', 'body', '', 'trim');

        // 为空判断
        if (empty($title)) {
            throw new HttpException(404, '标题未填写');
        }
        if (empty($body)) {
            throw new HttpException(404, '正文未填写');
        }

        // 分类有效性验证
        $category = M('postCategory')->getOwnerById($categoryId, $this->_user['id']);
        if ($category === false) {
            throw new HttpException(404, '分类获取失败');
        }
        if (empty($category) || intval($category['status']) !== PostCategoryModel::STATUS_NORMAL) {
            throw new HttpException(404, '分类不存在或状态异常');
        }

        // 数据处理
        $title = substr(htmlspecialchars($title, ENT_QUOTES), 0, 100);
        $body = htmlspecialchars($body, ENT_QUOTES);

        // 发布日志
        $postId = M('post')->publish($categoryId, $this->_user['id'], $title, $body);
        if ($postId === false) {
            throw new HttpException(404, '日志发布失败');
        }

        return [
            'postId' => $postId
        ];
    }

    /**
     * 修改日志
     */
    public function postModifyAction()
    {
        $postId = I('get', 'postId', 0, 'intval');

        // 日志
        $post = Application::getInstance()->getModelInstance('post')->getOwnerById($postId, $this->_user['id'], true);
        if (is_string($post)) {
            throw new HttpException(404, $post);
        }

        // 日志正文
        $postBody = Application::getInstance()->getModelInstance('postBody')->getByPostId($postId);
        if ($postBody === null) {
            throw new HttpException(404, '日志正文丢失');
        }
        $post['body'] = $postBody['body'];

        return [
            'user' => $this->_user,
            'post' => $post
        ];
    }

    /**
     * 修改日志 - 提交
     */
    public function postModifySubmitAction()
    {
        // todo
    }
}
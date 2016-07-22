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
        // 登录的地方已经做了状态判断，非正常状态的用户信息也不会到 SESSION 中，所以这里不再做状态判断
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
        return [
            'user' => $this->_user
        ];
    }

    /**
     * 发布日志 - 提交
     */
    public function postAddSubmitAction()
    {
        $title = I('post', 'title', '', 'htmlspecialchars');
        $body = I('post', 'body', '', 'htmlspecialchars');
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

    }
}
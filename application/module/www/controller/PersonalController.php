<?php
/**
 * PersonalController.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/22 15:02
 */

class PersonalController extends ControllerAbstract
{
    /**
     * @var array 当前登录的用户
     */
    private $_user = null;

    /**
     * 构造器
     *
     * @throws Exception
     */
    public function __construct()
    {
        if (isset($_SESSION['user'])) {
            $this->_user = $_SESSION['user'];
        } else {
            throw new \Exception('您尚未登录', 10032);
        }
    }

    /**
     * 首页
     */
    public function indexAction()
    {

    }

    /**
     * 发布日志
     *
     * @param Request $requestInstance
     * @param Response $responseInstance
     * @return array
     * @throws Exception
     */
    public function postAddAction(Request $requestInstance, Response $responseInstance)
    {
        // 分类列表
        $categories = Application::getInstance()->getModelInstance('postCategory')->getNormalListByUserId($this->_user['id']);
        if ($categories === false) {
            throw new \Exception('分类列表获取失败', 10032);
        }

        return [
            'user' => $this->_user,
            'categories' => $categories
        ];
    }

    /**
     * 发布日志 - 提交
     *
     * @param Request $requestInstance
     * @param Response $responseInstance
     * @return string
     * @throws Exception
     */
    public function postAddSubmitAction(Request $requestInstance, Response $responseInstance)
    {
        // post
        $title = $requestInstance->getGlobalVariable('post', 'title', '');
        $categoryId = $requestInstance->getGlobalVariable('post', 'categoryId', 0);
        $body = $requestInstance->getGlobalVariable('post', 'body', '');

        // post 处理
        $title = mb_substr(htmlspecialchars(trim($title), ENT_QUOTES), 0, 100, 'utf-8');
        $categoryId = intval($categoryId);
        $body = htmlspecialchars($body, ENT_QUOTES);

        // post 为空判断
        if (empty($title)) {
            throw new \Exception('缺少日志标题', 10037);
        }
        if (empty($body)) {
            throw new \Exception('缺少日志正文', 10038);
        }

        // 分类有效性验证（允许为 0，则发布到 “默认分类”）
        if ($categoryId !== 0) {
            $category = Application::getInstance()->getModelInstance('postCategory')->getOwnerById($categoryId, $this->_user['id']);
            if ($category === false) {
                throw new \Exception('分类获取失败', 10034);
            }
            if (empty($category) || intval($category['status']) !== PostCategoryModel::STATUS_NORMAL) {
                throw new \Exception('分类不存在或状态异常', 10035);
            }
        }

        // 发布日志
        $postId = Application::getInstance()->getModelInstance('post')->publish($categoryId, $this->_user['id'], $title, $body);
        if ($postId === false) {
            throw new \Exception('日志发布失败', 10036);
        }

        return $this->json($responseInstance, [
            'postId' => $postId
        ]);
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
     * 好友 - 博友
     */
    public function friendAction()
    {

    }

    /**
     * 设置 - 权限
     */
    public function accessAction()
    {

    }

    /**
     * 修改日志 - 提交
     */
    public function postModifySubmitAction()
    {
        // todo
    }
}
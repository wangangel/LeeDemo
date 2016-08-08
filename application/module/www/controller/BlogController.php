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
     * @var array 当前博主
     */
    private $_user = null;

    /**
     * @var array 当前博客主题
     */
    private $_theme = null;

    /**
     * 构造器
     *
     * @param Request $requestInstance
     * @param Response $responseInstance
     * @throws Exception
     */
    public function __construct(Request $requestInstance, Response $responseInstance)
    {
        $userId = $requestInstance->getGlobalVariable('get', 'userId', 0);

        // 博主
        $user = Application::getInstance()->getModelInstance('user')->getByPK($userId);
        if ($user === false) {
            throw new \Exception('用户信息获取失败', 10029);
        }
        if (empty($user)) {
            throw new \Exception('用户不存在', 10030);
        }
        if (intval($user['status']) !== UserModel::STATUS_NORMAL) {
            throw new \Exception('用户状态异常', 10031);
        }

        // 主题
        $theme = Application::getInstance()->getModelInstance('theme')->getByPK($user['theme_id']);
        if ($theme !== false && !empty($theme) && intval($theme['status']) === ThemeModel::STATUS_NORMAL) {
            $this->_theme = $theme;
        }

        $this->_user = $user;
    }

    /**
     * 首页
     *
     * @param Request $requestInstance
     * @param Response $responseInstance
     * @return array
     */
    public function indexAction(Request $requestInstance, Response $responseInstance)
    {
        return [
            'user' => $this->_user
        ];
    }

    /**
     * 日志列表
     *
     * @param Request $requestInstance
     * @param Response $responseInstance
     * @throws Exception
     * @return array
     */
    public function postListAction(Request $requestInstance, Response $responseInstance)
    {
        // get
        $categoryId = $requestInstance->getGlobalVariable('get', 'categoryId', 0);
        $page = $requestInstance->getGlobalVariable('get', 'page', 1);

        // get 处理
        $categoryId = intval($categoryId);
        $page = intval($page);

        // 当前分类
        $category = [];
        if ($categoryId !== 0) {
            $category = Application::getInstance()->getModelInstance('postCategory')->getOwnerById($categoryId, $this->_user['id']);
            if ($category === false) {
                throw new \Exception(404, '分类获取失败');
            }
            if (empty($category) || intval($category['status']) !== PostCategoryModel::STATUS_NORMAL) {
                throw new \Exception(404, '分类不存在或状态异常');
            }
        }

        // 日志列表
        $data = Application::getInstance()->getModelInstance('post')->getPagedList($page, $this->_user['id'], $categoryId, PostModel::STATUS_NORMAL);
        if ($data === false) {
            throw new \Exception(404, '日志列表获取失败');
        }

        // 分类列表
        $categoryList = Application::getInstance()->getModelInstance('postCategory')->getNormalListByUserId($this->_user['id']);
        if ($categoryList === false) {
            throw new \Exception(404, '分类列表获取失败');
        }

        return [
            'param' => [
              'categoryId' => $categoryId,
              'page' => $page
            ],
            'user' => $this->_user,
            'theme' => $this->_theme,
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
        $post = M('post')->getOwnerById($postId, $this->_user['id'], true);
        if ($post === false) {
            throw new HttpException(404, '日志获取失败');
        }
        if (empty($post) || intval($post['status']) !== PostModel::STATUS_NORMAL) {
            throw new HttpException(404, '日志不存在或状态异常');
        }

        // 日志正文
        $postBody = M('postBody')->getByPostId($postId);
        if ($postBody === false) {
            throw new HttpException(404, '日志正文获取失败');
        }
        if (empty($postBody)) {
            throw new HttpException(404, '日志正文不存在');
        }
        $post['body'] = $postBody['body'];

        return [
            'user' => $this->_user,
            'post' => $post
        ];
    }

    /**
     * 个人档
     */
    public function profileAction()
    {
        return [
            'user' => $this->_user
        ];
    }
}
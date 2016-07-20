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
     * 构造器
     */
    public function __constrcut()
    {

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
     */
    public function postListAction()
    {
        $data = M('post')->getPagedList();

        return [
            'data' => $data
        ];
    }

    /**
     * 日志正文
     */
    public function postAction()
    {
        $postId = I('get', 'postId', 0, 'intval');

        $post = M('post')->getById($postId);
        if (empty($post)) {
            throw new HttpException(404, '该文章不存在');
        } else {
            $post = $post[0];
            $status = intval($post['status']);

            if ($status === 0) {
                throw new HttpException(404, '无效的文章状态');
            } elseif ($status === 1) {
                throw new HttpException(404, '该文章正在审核中');
            } elseif ($status === 3) {
                throw new HttpException(404, '该文章已被删除');
            }
        }

        $postBody = M('postBody')->getById($postId);
        if (empty($postBody)) {
            throw new HttpException(404, '文章正文丢失');
        }
        $post['body'] = $postBody[0]['body'];

        return [
            'user' => '',
            'post' => $post
        ];
    }
}
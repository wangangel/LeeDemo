<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>日志</title>
    <link rel="stylesheet" type="text/css" href="/css/style.css">
</head>
<body>
    <div class="BL_wrap">
        <div class="BL_content">
            <?php include dirname(__DIR__) . '/top.php' ; ?>
            <?php include dirname(__DIR__) . '/blog_header.php' ; ?>
            <div class="BL_main">
                <div class="BL_primary">
                    <div class="box">
                        <div class="bx-title">
                            <h3><?php echo $param['categoryId'] === 0 ? '日志列表' : $category['name']; ?></h3>
                            <?php if(isset($_SESSION['user'])): ?>
                                <em><a href="/?c=personal&a=postAdd">发布日志</a></em>
                            <?php endif; ?>
                        </div>
                        <div class="bx-body">
                            <div class="post-list">
                                <?php if(empty($data['list'])): ?>
                                    <p>该栏目暂无日志</p>
                                <?php else: ?>
                                    <ul>
                                        <?php foreach($data['list'] as $post): ?>
                                            <li>
                                                <div class="pl-title">
                                                    <strong><a href="/?c=blog&a=post&userId=<?php echo $user['id']; ?>&postId=<?php echo $post['id']; ?>"><?php echo $post['title']; ?></a></strong>
                                                    <?php if(intval($post['is_recommend']) === 1): ?>
                                                        <i class="icon icon-recommend"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="pl-description">
                                                    <p><?php echo $post['description']; ?> ...</p>
                                                </div>
                                                <div class="pl-info">
                                                    <span>
                                                        <a href="javascript:;">阅读(<?php echo $post['count_view']; ?>)</a>
                                                        <a href="javascript:;">喜欢(<?php echo $post['count_like']; ?>)</a>
                                                        <a href="javascript:;">转载(<?php echo $post['count_repost']; ?>)</a>
                                                        <a href="javascript:;">评论(<?php echo $post['count_comment']; ?>)</a>
                                                        <?php if(isset($_SESSION['user'])): ?>
                                                            <a href="/?c=publish&a=postModify&postId=<?php echo $post['id']; ?>">[修改]</a>
                                                            <a href="javascript:;">[删除]</a>
                                                        <?php endif; ?>
                                                    </span>
                                                    <em><a href="/?c=blog&a=post&userId=<?php echo $user['id']; ?>&postId=<?php echo $post['id']; ?>">阅读全文 &raquo;</a></em>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="BL_sidebar">
                    <div class="box">
                        <div class="bx-title">
                            <h3>日志分类</h3>
                        </div>
                        <div class="bx-body">
                            <div class="widget widget-list">
                                <ul>
                                    <?php foreach($categoryList as $category): ?>
                                        <li><a href="/?c=blog&a=postList&userId=<?php echo $user['id']; ?>&categoryId=<?php echo $category['id']; ?>"><?php echo $category['name']; ?></a><em>(<?php echo $category['count_normal_post']; ?>)</em></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php echo $user['count_normal_post']; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
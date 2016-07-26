<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>日志</title>
    <link rel="stylesheet" type="text/css" href="/css/style.css">
</head>
<body>
    <div class="wrap">
        <div class="content">
            <?php include dirname(__DIR__) . '/blog_header.php' ; ?>
            <div class="main">
                <div class="primary">
                    <div class="post-list">
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
                                        <p><?php echo $post['description']; ?></p>
                                    </div>
                                    <div class="pl-info">
                                        <?php if(isset($_SESSION['user'])): ?>
                                            <span>
                                                <a href="/?c=publish&a=postModify&postId=<?php echo $post['id']; ?>">修改</a>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="sidebar">
                    <?php if(isset($_SESSION['user'])): ?>
                        <div class="post-publish">
                            <a href="/?c=publish&a=postAdd">发布日志</a>
                        </div>
                    <?php endif; ?>
                    <div class="box">
                        <div class="bx-title">
                            <h3>日志分类</h3>
                        </div>
                        <div class="bx-body">
                            <ul>
                                <?php foreach($categoryList as $category): ?>
                                    <li><a href="/?c=blog&a=postList&userId=<?php echo $user['id']; ?>&categoryId=<?php echo $category['id']; ?>"><?php echo $category['name']; ?></a><em>(<?php echo $category['count_normal_post']; ?>)</em></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
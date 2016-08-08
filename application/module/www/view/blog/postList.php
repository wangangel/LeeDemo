<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>日志</title>
    <?php if($theme === null): ?>
        <link rel="stylesheet" type="text/css" href="/asset/v1.0/css/style.css">
    <?php else: ?>
        <link rel="stylesheet" type="text/css" href="/theme/<?php echo $theme['path']; ?>/css/style.css">
    <?php endif; ?>
</head>
<body>
    <?php include dirname(__DIR__) . '/top.php' ; ?>
    <div class="wrap">
        <div class="container">
            <?php include dirname(__DIR__) . '/blog_header.php' ; ?>
            <div class="content">
                <div class="main">
                    <div class="box">
                        <div class="box-title">
                            <h3><?php echo $param['categoryId'] === 0 ? '日志列表' : $category['name']; ?></h3>
                            <?php if(isset($_SESSION['user'])): ?>
                                <em><a href="/?c=personal&a=postAdd">发布日志</a></em>
                            <?php endif; ?>
                        </div>
                        <div class="box-body">
                            <div class="post-list">
                                <?php if(empty($data['list'])): ?>
                                    <p>该栏目暂无日志</p>
                                <?php else: ?>
                                    <ul>
                                        <?php foreach($data['list'] as $post): ?>
                                            <li>
                                                <div class="post-list-title">
                                                    <strong><a href="/?c=blog&a=post&userId=<?php echo $user['id']; ?>&postId=<?php echo $post['id']; ?>"><?php echo $post['title']; ?></a></strong>
                                                    <?php if(intval($post['is_recommend']) === 1): ?>
                                                        <i class="icon icon-recommend"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="post-list-info">
                                                    <em><?php echo intval($post['count_comment']) === 0 ? '暂无评论' : '<a href="">' . $post['count_comment'] . ' 条评论</a>'; ?></em>
                                                    <span><?php echo date('Y-m-d H:i', $post['time_publish']); ?></span>
                                                </div>
                                                <div class="post-list-description">
                                                    <p><?php echo $post['description']; ?> ...</p>
                                                </div>
                                                <div class="post-list-under">
                                                    <?php if(isset($_SESSION['user'])): ?>
                                                        <span><a href="/?c=publish&a=postModify&postId=<?php echo $post['id']; ?>">修改</a></span>
                                                        <span><a href="javascript:;">删除</a></span>
                                                    <?php endif; ?>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sidebar">
                    <div class="box">
                        <div class="box-title">
                            <h3>日志分类</h3>
                        </div>
                        <div class="box-body">
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
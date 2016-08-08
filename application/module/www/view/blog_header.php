<div class="header">
    <div class="header-caption">
        <h1><?php echo empty($user['blog_title']) ? $user['nickname'] . '的博客' : $user['blog_title']; ?></h1>
        <p>Previewing Another WordPress Blog</p>
    </div>
    <div class="header-menu">
        <ul>
            <li><a class="home" href="/?c=blog&userId=<?php echo $user['id']; ?>">首页</a></li>
            <li><a href="/?c=blog&a=postList&userId=<?php echo $user['id']; ?>">日志</a></li>
            <li><a href="">相册</a></li>
            <li><a href="/?c=blog&a=profile&userId=<?php echo $user['id']; ?>">个人档</a></li>
            <li><a href="">博友</a></li>
            <li><a class="last" href="javascript:;"></a></li>
        </ul>
    </div>
</div>
<div class="top">
    <?php if(isset($_SESSION['user'])): ?>
        <?php echo $_SESSION['user']['nickname']; ?> <a href="/?c=access&a=logout">退出</a>
    <?php else: ?>
        <a href="/?c=access&a=register">注册</a>
        <a href="/?c=access&a=login">登陆</a>
    <?php endif; ?>
</div>
<div class="header">
    <div class="hd-menu">
        <ul>
            <li><a href="/?c=blog&userId=<?php echo $user['id']; ?>">首页</a></li>
            <li><a href="/?c=blog&a=postList&userId=<?php echo $user['id']; ?>">日志</a></li>
            <li><a href="/?c=blog&a=profile&userId=<?php echo $user['id']; ?>">个人档</a></li>
        </ul>
    </div>
</div>
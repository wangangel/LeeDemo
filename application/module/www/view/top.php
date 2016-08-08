<div class="top">
    <?php if(isset($_SESSION['user'])): ?>
        <a href="/?c=personal"><?php echo $_SESSION['user']['nickname']; ?></a>，<a href="/?c=access&a=logout">退出</a>
    <?php else: ?>
        <a href="/?c=access&a=register">注册</a>
        <a href="/?c=access&a=login">登陆</a>
    <?php endif; ?>
</div>
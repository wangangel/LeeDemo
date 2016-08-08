<div class="col-md-2">
    <div class="list-group">
        <a href="/?c=personal" class="list-group-item<?php echo !isset($_GET['a']) ? ' active' : ''; ?>">总览</a>
        <a href="/?c=personal&a=postAdd" class="list-group-item">写日志</a>
        <a href="#" class="list-group-item">传照片</a>
        <a href="/?c=personal&a=friend" class="list-group-item<?php echo $_GET['a'] === 'friend' ? ' active' : ''; ?>">好友</a>
        <a href="/?c=personal&a=access" class="list-group-item<?php echo $_GET['a'] === 'access' ? ' active' : ''; ?>">设置</a>
    </div>
</div>
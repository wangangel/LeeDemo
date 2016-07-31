<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>完善注册信息 - 注册账号</title>
    <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/css/style.css">
</head>
<body>
    <div class="BL_wrap">
        <div class="BL_content">
            <div class="BL_main">
                <div class="access-form">
                    <div class="page-header">
                        <h3>注册账号 <small>完善注册信息</small></h3>
                    </div>
                    <?php if($emailVerify['token'] !== $tokenGet): ?>
                        <div class="alert alert-danger">验证邮件校验失败！您可以尝试 <a href="/?c=access&a=register">重新发送</a></div>
                    <?php else: ?>
                        <form action="javascript:;" onsubmit="access.registerVerify()">
                            <div id="alertSuccess" class="alert alert-success" style="display:none;">注册成功！正在为您跳转...</div>
                            <div id="alertDanger" class="alert alert-danger" style="display:none;"></div>
                            <div class="form-group">
                                <label>邮箱地址</label>
                                <input class="form-control" type="text" value="<?php echo $emailVerify['email']; ?>" readonly disabled>
                            </div>
                            <div class="form-group">
                                <label>密码</label>
                                <input id="password" class="form-control" type="password" maxlength="15">
                            </div>
                            <div class="form-group">
                                <label>再输一次密码</label>
                                <input id="passwordConfirm" class="form-control" type="password" maxlength="15">
                            </div>
                            <div class="form-group">
                                <label>昵称</label>
                                <input id="nickname" class="form-control" type="text" maxlength="15">
                            </div>
                            <div class="form-group">
                                <input id="submit" class="btn btn-primary" type="submit" value="完成注册">
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="/js/custom.js"></script>
</body>
</html>
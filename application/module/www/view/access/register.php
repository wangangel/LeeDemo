<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>发送验证邮件 - 注册账号</title>
    <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/css/style.css">
</head>
<body>
    <div class="BL_wrap">
        <div class="BL_content">
            <div class="BL_main">
                <div class="access-form">
                    <div class="page-header">
                        <h3>注册账号 <small>发送验证邮件</small></h3>
                    </div>
                    <form action="javascript:;" onsubmit="access.registerMailSend()">
                        <div id="alertSuccess" class="alert alert-success" style="display:none;">邮件发送成功！</div>
                        <div id="alertDanger" class="alert alert-danger" style="display:none;"></div>
                        <div class="form-group">
                            <label>邮箱地址</label>
                            <input id="email" class="form-control" type="text" maxlength="50">
                        </div>
                        <div class="form-group">
                            <label>验证码</label>
                            <div class="input-group">
                                <input id="captcha" class="form-control" type="text" maxlength="5">
                                <span class="input-group-addon"><img id="captchaImage" src="/?a=captcha" title="点击更换验证码" onclick="this.src='/?a=captcha&refresh='+Math.random();"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <input id="submit" class="btn btn-primary" type="submit" value="发送验证邮件">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="/js/custom.js"></script>
</body>
</html>
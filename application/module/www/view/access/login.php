<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>登录</title>
    <link rel="stylesheet" type="text/css" href="/vendor/bootstrap-3.3.5-dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/css/style.css">
</head>
<body>
    <div class="BL_wrap">
        <div class="BL_content">
            <div class="BL_main">
                <div class="access-form">
                    <div class="page-header">
                        <h3>登录</h3>
                    </div>
                    <form id="loginForm" action="javascript:;">
                        <div id="alertSuccess" class="alert alert-success" style="display:none;"></div>
                        <div id="alertDanger" class="alert alert-danger" style="display:none;"></div>
                        <div class="form-group">
                            <label>邮箱地址</label>
                            <input id="email" name="email" class="form-control" type="text" maxlength="50">
                        </div>
                        <div class="form-group">
                            <label>密码</label>
                            <input id="password" name="password" class="form-control" type="password" maxlength="15">
                        </div>
                        <div class="form-group captcha">
                            <label>验证码</label>
                            <input id="captcha" name="captcha" class="form-control" type="text" maxlength="5">
                            <span class="captcha-image"><img id="captchaImage" src="/?a=captcha" title="点击更换验证码" onclick="this.src='/?a=captcha&refresh='+Math.random();"></span>
                        </div>
                        <div class="form-group">
                            <input id="submit" class="btn btn-primary" type="submit" value="登录博客">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="/vendor/jquery/jquery-3.1.0.min.js"></script>
    <script src="/vendor/jquery-validation-1.15.0-dist/jquery.validate.min.js"></script>
    <script src="/vendor/jquery-validation-1.15.0-dist/additional-methods.min.js"></script>
    <script>
        $('#loginForm').validate({
            onsubmit: true,
            onfocusout: false,
            onkeyup: false,
            errorElement: 'span',
            errorClass: 'help-block',
            rules: {
                email: {
                    required: true,
                    pattern: /^([a-zA-Z0-9_\.\-]+)\@(qq|163)\.com$/
                },
                password: {
                    required: true,
                    minlength: 5
                },
                captcha: {
                    required: true,
                    minlength: 5
                }
            },
            messages: {
                email: {
                    required: '请填写邮箱地址',
                    pattern: '请填写注册时的邮箱地址，目前仅支持 QQ/163 邮箱'
                },
                password: {
                    required: '请填写密码',
                    minlength: '密码长度不能少于 5 位'
                },
                captcha: {
                    required: '请填写验证码',
                    minlength: '验证码长度为 5 位'
                }
            },
            highlight: function(ele) {
                $(ele).closest('div').addClass('has-error');
            },
            unhighlight: function(ele) {
                $(ele).closest('div').removeClass('has-error');
            },
            submitHandler: function() {
                $('#alertSuccess').hide();
                $('#alertDanger').hide();
                $('#submit').attr('disabled', true);
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    url: '/?c=access&a=loginSubmit',
                    timeout: 5000,
                    data: {
                        email: $('#email').val(),
                        password: $('#password').val(),
                        captcha: $('#captcha').val()
                    },
                    success: function(json) {
                        if (json.status === true) {
                            $('#alertSuccess').html('登录成功！正在跳往至首页...').show();
                            setTimeout('window.location.href = "/?c=blog&userId=' + json.data.userId + '"', 1000);
                        } else {
                            $('#alertDanger').html(json.message).show();
                            $('#submit').attr('disabled', false);
                        }
                        $('#captchaImage').attr('src', '/?a=captcha&refresh=' + Math.random());
                    },
                    error: function() {
                        $('#captchaImage').attr('src', '/?a=captcha&refresh=' + Math.random());
                        $('#alertDanger').html('可能网络异常导致操作失败').show();
                        $('#submit').attr('disabled', false);
                    }
                });
            }
        });
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>完善注册信息 - 注册</title>
    <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/css/style.css">
</head>
<body>
    <div class="BL_wrap">
        <div class="BL_content">
            <div class="BL_main">
                <div class="access-form">
                    <div class="page-header">
                        <h3>注册 <small>完善帐号信息</small></h3>
                    </div>
                    <?php if($emailVerify['token'] !== $tokenGet): ?>
                        <div class="alert alert-danger">验证邮件校验失败！您可以尝试 <a href="/?c=access&a=register">重新发送</a></div>
                    <?php else: ?>
                        <form id="registerVerifyForm" action="javascript:;">
                            <div id="alertSuccess" class="alert alert-success" style="display:none;"></div>
                            <div id="alertDanger" class="alert alert-danger" style="display:none;"></div>
                            <div class="form-group">
                                <label>邮箱地址</label>
                                <input class="form-control" type="text" value="<?php echo $emailVerify['email']; ?>" readonly disabled>
                            </div>
                            <div class="form-group">
                                <label>密码</label>
                                <input id="password" name="password" class="form-control" type="password" maxlength="15">
                            </div>
                            <div class="form-group">
                                <label>再输一次密码</label>
                                <input id="passwordConfirm" name="passwordConfirm" class="form-control" type="password" maxlength="15">
                            </div>
                            <div class="form-group">
                                <label>昵称</label>
                                <input id="nickname" name="nickname" class="form-control" type="text" maxlength="15">
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
    <script src="/js/jquery-3.1.0.min.js"></script>
    <script src="/js/jquery.validate.min.js"></script>
    <script src="/js/additional-methods.min.js"></script>
    <script>
        $('#registerVerifyForm').validate({
            onsubmit: true,
            onfocusout: false,
            onkeyup: false,
            errorElement: 'span',
            errorClass: 'help-block',
            rules: {
                password: {
                    required: true,
                    pattern: /^[\@A-Za-z0-9\!\#\$\%\^\&\*\.\-\_\~]{5,15}$/
                },
                passwordConfirm: {
                    required: true,
                    equalTo: '#password'
                },
                nickname: {
                    required: true,
                    pattern: /^[^\s]{2,15}$/
                }
            },
            messages: {
                password: {
                    required: '请填写密码',
                    pattern: '密码长度为 5~15 位，可包含字母数字特殊字符'
                },
                passwordConfirm: {
                    required: '请再填写一次密码',
                    equalTo: '两次输入的密码不一致'
                },
                nickname: {
                    required: '请填写昵称',
                    pattern: '昵称长度为 3~15 位，不能包含空格'
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
                    url: '/?c=access&a=registerVerifySubmit',
                    timeout: 5000,
                    data: {
                        password: $('#password').val(),
                        nickname: $('#nickname').val()
                    },
                    success: function(json) {
                        if (json.status === true) {
                            $('#alertSuccess').html('注册成功！正在跳往至首页...').show();
                            setTimeout('window.location.href = "/"', 1000);
                        } else {
                            $('#captchaImage').attr('src', '/?a=captcha&refresh='+Math.random());
                            $('#alertDanger').html(json.message).show();
                            $('#submit').attr('disabled', false);
                        }
                    },
                    error: function() {
                        $('#captchaImage').attr('src', '/?a=captcha&refresh='+Math.random());
                        $('#alertDanger').html('可能网络异常导致操作失败').show();
                        $('#submit').attr('disabled', false);
                    }
                });
            }
        });
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>登录</title>
    <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
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
                    <form action="javascript:;" onsubmit="access.registerMailSend()">
                        <div id="alertSuccess" class="alert alert-success" style="display:none;">登录成功！正在为您跳转...</div>
                        <div id="alertDanger" class="alert alert-danger" style="display:none;"></div>
                        <div class="form-group">
                            <label>邮箱地址</label>
                            <input id="email" class="form-control" type="text" maxlength="50">
                        </div>
                        <div class="form-group">
                            <label>密码</label>
                            <input id="password" class="form-control" type="text" maxlength="15">
                        </div>
                        <div class="form-group">
                            <input id="submit" class="btn btn-primary" type="submit" value="登录博客">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php var_dump(Application::getInstance()->getConfigInstance()->get()); ?>
    </div>
    <script src="/js/custom.js"></script>
    <script>
        var validate = {
            rules: {
                email: {
                    required: true,
                    pattern: /^([a-zA-Z0-9_\.\-]+)\@(qq|163)\.com$/
                },
                password: {
                    required: true,
                    minLength: 5
                }
            },
            messages: {
                email: {
                    required: '请输入邮箱地址',
                    pattern: '请输入有效的邮箱地址，目前仅支持 QQ/163 邮箱'
                },
                password: {
                    required: '请输入密码',
                    minLength: '密码不能少于 5 位'
                }
            }
        };

        $().validate();
    </script>
</body>
</html>
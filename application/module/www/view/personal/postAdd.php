<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>发布日志</title>
    <link rel="stylesheet" type="text/css" href="/vendor/bootstrap-3.3.5-dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/vendor/summernote-0.8.1-dist/summernote.css">
    <link rel="stylesheet" type="text/css" href="/css/style.css">
</head>
<body class="personal">
    <div class="BL_wrap">
        <div class="BL_content">
            <?php include dirname(__DIR__) . '/top.php' ; ?>
            <?php include dirname(__DIR__) . '/personal_header.php' ; ?>
            <form id="postAddForm" action="javascript:;">
                <div id="alertSuccess" class="alert alert-success" style="display:none;"></div>
                <div id="alertDanger" class="alert alert-danger" style="display:none;"></div>
                <div class="form-group">
                    <label>日志标题</label>
                    <input id="title" name="title" class="form-control" type="text" maxlength="100">
                </div>
                <div class="form-group">
                    <label>选择分类</label>
                    <select id="categoryId" class="form-control">
                        <option value="0">默认分类</option>
                        <?php foreach($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>日志正文</label>
                    <div id="body"></div>
                </div>
                <div class="form-group">
                    <input id="submit" type="submit" class="btn btn-primary" value="发布日志">
                </div>
            </form>
        </div>
    </div>
    <script src="/vendor/jquery/jquery-3.1.0.min.js"></script>
    <script src="/vendor/jquery-validation-1.15.0-dist/jquery.validate.min.js"></script>
    <script src="/vendor/jquery-validation-1.15.0-dist/additional-methods.min.js"></script>
    <script src="/vendor/bootstrap-3.3.5-dist/js/bootstrap.min.js"></script>
    <script src="/vendor/summernote-0.8.1-dist/summernote.min.js"></script>
    <script src="/vendor/summernote-0.8.1-dist/lang/summernote-zh-CN.js"></script>
    <script>
        // todo: validate 和 summernote 并存时回车提交表单会失效
        $(document).ready(function() {
            $('#body').summernote({
                height: 350,
                lang: 'zh-CN'
            });
        });
        $('#postAddForm').validate({
            onsubmit: true,
            onfocusout: false,
            onkeyup: false,
            errorElement: 'span',
            errorClass: 'help-block',
            rules: {
                title: {
                    required: true,
                    minlength: 10
                }
            },
            messages: {
                title: {
                    required: '请填写标题',
                    minlength: '标题长度为 10~100 个字'
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
                    url: '/?c=personal&a=postAddSubmit',
                    timeout: 5000,
                    data: {
                        title: $('#title').val(),
                        categoryId: $('#categoryId').val(),
                        body: $('#body').summernote('code')
                    },
                    success: function(json) {
                        if (json.status === true) {
                            $('#alertSuccess').html('发布成功！正在跳往至日志列表...').show();
                            setTimeout('window.location.href = "/?c=blog&a=postList&userId=<?php echo $user['id']; ?>"', 1000);
                        } else {
                            $('#alertDanger').html(json.message).show();
                            $('#submit').attr('disabled', false);
                        }
                    },
                    error: function() {
                        $('#alertDanger').html('可能网络异常导致操作失败').show();
                        $('#submit').attr('disabled', false);
                    }
                });
            }
        });
    </script>
</body>
</html>
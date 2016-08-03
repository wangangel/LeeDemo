<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>发布日志</title>
    <link rel="stylesheet" type="text/css" href="/vendor/bootstrap-3.3.5-dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/vendor/summernote-0.8.1-dist/summernote.css">
    <link rel="stylesheet" type="text/css" href="/css/style.css">
</head>
<body>
    <div class="BL_wrap">
        <div class="BL_content">
            <?php include dirname(__DIR__) . '/top.php' ; ?>
            <div class="main">
                <div class="col-md-2">
                    <ul class="nav nav-pills nav-stacked">
                        <li class="active"><a href="#">发布日志</a></li>
                        <li><a href="#">SVN</a></li>
                    </ul>
                </div>
                <div class="col-md-10">
                    <div class="form-group">
                        <label>日志标题</label>
                        <input id="title" class="form-control" type="text">
                    </div>
                    <div class="form-group">
                        <label>选择分类</label>
                        <select id="category" class="form-control">
                            <option value="0">--- 请选择 ---</option>
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
                        <a class="btn btn-primary" href="">发布日志</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/vendor/jquery/jquery-3.1.0.min.js"></script>
    <script src="/vendor/bootstrap-3.3.5-dist/js/bootstrap.min.js"></script>
    <script src="/vendor/summernote-0.8.1-dist/summernote.min.js"></script>
    <script src="/vendor/summernote-0.8.1-dist/lang/summernote-zh-CN.js"></script>
    <script>
        $(document).ready(function() {
            $('#body').summernote({
                height: 350,
                lang: 'zh-CN'
            });
        });
    </script>
</body>
</html>
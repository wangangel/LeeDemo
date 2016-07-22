<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>发布日志</title>
    <link rel="stylesheet" type="text/css" href="/css/style.css">
</head>
<body>
    <div class="wrap">
        <div class="content">
            <?php include dirname(__DIR__) . '/blog_header.php' ; ?>
            <div class="main">
                <div class="primary">
                    <div id="editor"></div>
                    <a href="javascript:publish.postAdd();">发布</a>
                </div>
                <div class="sidebar">
                    123
                </div>
            </div>
        </div>
    </div>
    <script src="/vendor/kindeditor-4.1.11-zh-CN/kindeditor-all-min.js"></script>
    <script src="/js/custom.js"></script>
    <script>
        KindEditor.ready(function(K) {
            window.editor = K.create('#editor', {
                width: '700px',
                height: '300px'
            });
        });
    </script>
</body>
</html>
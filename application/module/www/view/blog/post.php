<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title><?php echo $post['title']; ?></title>
    <link rel="stylesheet" type="text/css" href="/css/style.css">
</head>
<body>
    <div class="wrap">
        <div class="content">
            <?php include dirname(__DIR__) . '/header.php' ; ?>
            <div class="main">
                <div class="primary">
                    <div class="post">
                        <div class="pt-title">
                            <h1><?php echo $post['title']; ?></h1>
                        </div>
                        <div class="pt-body">
                            <?php echo $post['body']; ?>
                        </div>
                    </div>
                </div>
                <div class="sidebar">
                    aaa
                </div>
            </div>
        </div>
    </div>
</body>
</html>
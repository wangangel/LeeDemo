<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>博友 - 好友 - 个人中心</title>
    <link rel="stylesheet" type="text/css" href="/vendor/bootstrap-3.3.5-dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/asset/v1.0/css/style.css">
</head>
<body class="access">
    <div class="wrap">
        <div class="container">
            <?php include dirname(__DIR__) . '/top.php' ; ?>
            <?php include dirname(__DIR__) . '/personal_header.php' ; ?>
            <div class="row">
                <?php include dirname(__DIR__) . '/personal_left.php' ; ?>
                <div class="col-md-10">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">博友</h3>
                        </div>
                        <div class="panel-body">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="">博友</a></li>
                                <li><a href="/?c=personal&a=visitor">访客</a></li>
                                <li><a href="/?c=personal&a=blacklist">黑名单</a></li>
                            </ul>
                            <form id="accessForm" action="javascript:;">
                                asd
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/vendor/jquery/jquery-3.1.0.min.js"></script>
    <script src="/vendor/jquery-validation-1.15.0-dist/jquery.validate.min.js"></script>
    <script src="/vendor/jquery-validation-1.15.0-dist/additional-methods.min.js"></script>
    <script src="/vendor/bootstrap-3.3.5-dist/js/bootstrap.min.js"></script>
    <script>

    </script>
</body>
</html>
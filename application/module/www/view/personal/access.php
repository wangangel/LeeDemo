<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>权限 - 设置 - 个人中心</title>
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
                            <h3 class="panel-title">权限</h3>
                        </div>
                        <div class="panel-body">
                            <ul class="nav nav-tabs">
                                <li><a href="/?c=personal&a=profile">个人信息</a></li>
                                <li class="active"><a href="">权限</a></li>
                                <li><a href="/?c=personal&a=account">帐号</a></li>
                            </ul>
                            <form id="accessForm" action="javascript:;">
                                <div class="form-group">
                                    <label>谁可以访问我的博客</label>
                                    <div>
                                        <label class="checkbox-inline">
                                            <input type="radio" name="optionsRadiosinline21" value="option2" checked> 所有人
                                        </label>
                                        <label class="checkbox-inline">
                                            <input type="radio" name="optionsRadiosinline21" value="option2"> 博友
                                        </label>
                                        <label class="checkbox-inline">
                                            <input type="radio" name="optionsRadiosinline21" value="option2"> 仅自己
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>谁可以评论我的日志、照片</label>
                                    <div>
                                        <label class="checkbox-inline">
                                            <input type="radio" name="optionsRadiosinline212" value="option2" checked> 所有人
                                        </label>
                                        <label class="checkbox-inline">
                                            <input type="radio" name="optionsRadiosinline212" value="option2"> 博友
                                        </label>
                                        <label class="checkbox-inline">
                                            <input type="radio" name="optionsRadiosinline212" value="option2"> 关闭评论
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>谁可以给我留言</label>
                                    <div>
                                        <label class="checkbox-inline">
                                            <input type="radio" name="optionsRadiosinline2123" value="option2" checked> 所有人
                                        </label>
                                        <label class="checkbox-inline">
                                            <input type="radio" name="optionsRadiosinline2123" value="option2"> 博友
                                        </label>
                                        <label class="checkbox-inline">
                                            <input type="radio" name="optionsRadiosinline2123" value="option2"> 关闭留言
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>日志能否被转载</label>
                                    <div>
                                        <div>
                                            <label class="checkbox-inline">
                                                <input type="radio" name="optionsRadiosinline2" value="option2" checked> 可以转载
                                            </label>
                                            <label class="checkbox-inline">
                                                <input type="radio" name="optionsRadiosinline2" value="option2"> 禁止转载
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input id="submit" type="submit" class="btn btn-success" value="保存设置">
                                </div>
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
<?php
/**
 * exceptionCode.php
 *
 * User: 670554666@qq.com
 * Date: 2016/8/1 10:59
 */

return [
    'exceptionCode' => [
        10000 => '当前应用的初始化文件 Bootstrap.php 丢失',
        10001 => '当前应用的初始化文件 Bootstrap.php 中未定义 Bootstrap 类',
        10002 => '控制器文件丢失',
        10003 => '控制器类未定义',
        10004 => '控制器下未定义指定的动作',
        10005 => '模型文件丢失',
        10006 => '模型类未定义',
        10007 => '配置文件丢失',
        10008 => '配置不存在',
        10009 => '视图文件丢失',
        10010 => 'Memcache 驱动异常',
        10011 => 'Memcache 服务器添加失败',
        10012 => 'Mysqli 连接失败',
        10013 => 'Mysqli select() 缺少表名',
        10014 => 'Mysqli insert() 缺少表名',
        10015 => 'Mysqli update() 缺少表名',
        10016 => '第三方类库不存在',
        10017 => '邮件发送失败',
        10018 => '无效的 Config::load() 参数',
        10019 => '验证码校验失败',
        10020 => '根据邮箱和密码查询用户失败',
        10021 => '根据邮箱和密码查询用户未找到',
        10022 => '用户状态异常',
        10023 => '当前已经处于登录状态',
        10024 => '输入内容不完整',
        10025 => '该邮箱已被使用',
        10026 => '验证邮件已过期',
        10027 => '帐号注册失败',
    ]
];
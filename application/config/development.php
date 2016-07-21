<?php
/**
 * development.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/6 14:34
 */

return [
    // 数据库配置
    'db' => [
        // 当前驱动（请注意大小写）
        'driver' => 'Mysqlii',

        // 主库
        'master' => [
            'host' => 'localhost',
            'dbname' => 'blog',
            'username' => 'root',
            'password' => '',
            'charset' => 'UTF8'
        ],

        // 从库
        'slave' => [
            [
                'host' => 'localhost',
                'dbname' => 'blog',
                'username' => 'root',
                'password' => '',
                'charset' => 'UTF8'
            ],
            [
                'host' => 'localhost',
                'dbname' => 'blog',
                'username' => 'root',
                'password' => '',
                'charset' => 'UTF8'
            ]
        ]
    ],

    // 显示设置
    'display' => [
        'postNumPerPage' => 15  // 日志列表每页数量
    ]
];
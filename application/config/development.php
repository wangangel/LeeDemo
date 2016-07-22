<?php
/**
 * development.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/6 14:34
 */

return [
    // 数据库配置
    'database' => [
        // 当前驱动
        'driver' => 'mysqliX',

        // 主库
        'master' => [
            'host' => 'localhost',
            'dbname' => 'blog',
            'username' => 'root',
            'password' => '',
            'charset' => 'UTF8'
        ],

        // 从库
        'slaves' => [
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

    // 缓存配置
    'cache' => [
        'driver' => 'memcacheX',

        'servers' => [
            [
                'HOST' => '127.0.0.1',
                'PORT' => 11211
            ]
        ]
    ],

    // 显示设置
    'display' => [
        'postNumPerPage' => 15  // 日志列表每页数量
    ]
];
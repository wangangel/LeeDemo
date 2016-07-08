<?php
/**
 * index.php
 *
 * www 入口
 *
 * User: 670554666@qq.com
 * Date: 2016/7/5 18:42
 */

/**
 * 常量定义
 */
define('ENV', 'development');   // 当前环境，三个值可选：development/test/production
define('MODULE', 'www');        // 当前位于 /application/module 下的应用目录名

/**
 * 加载启动文件
 */
require dirname(dirname(__DIR__)) . '/library/__start.php';
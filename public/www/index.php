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
 * 加载 Application.php
 */
require dirname(dirname(__DIR__)) . '/library/Application.php';

/**
 * 常量定义
 */
define('ENV', 'development');               // 当前环境，三个值可选：development/test/production
define('MODULE', 'www');                    // 当前位于 /application/module 下的应用目录名
define('ROOT', dirname(dirname(__DIR__)));  // 根目录
define('SEP', DIRECTORY_SEPARATOR);         // 当前系统下的路径分隔符

/**
 * 执行
 */
try{
    library\Application::getInstance()->run();
} catch (\Exception $e) {
    echo $e->getMessage();
}
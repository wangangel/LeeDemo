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
define('MODULE', 'www');                    // 当前应用目录名
define('ROOT', dirname(dirname(__DIR__)));  // 根目录

/**
 * 执行
 */
library\Application::getInstance()->run();
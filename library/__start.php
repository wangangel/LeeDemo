<?php
/**
 * __start.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/8 20:27
 */

/**
 * 常量定义
 */
define('ROOT', dirname(__DIR__));       // 根目录
define('SEP', DIRECTORY_SEPARATOR);     // 当前系统下的路径分隔符

/**
 * 加载 Application
 */
require ROOT . '/library/Application.php';

/**
 * 执行
 */
try{
    library\Application::getInstance()->run();
} catch (\Exception $e) {
    echo $e->getMessage();
}
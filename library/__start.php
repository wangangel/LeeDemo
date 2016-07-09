<?php
/**
 * __start.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/9 13:30
 */

/**
 * 版本检查（5.3 开始支持 namespace，5.4 开始支持数组简写，所以版本限制到 5.4）
 */
if (version_compare(PHP_VERSION, '5.4.0', '<')) {
    exit('PHP 版本不能低于 5.4');
}

/**
 * 开始用量
 */
define('START_TIME', microtime(true));              // 开始执行的时间
define('START_MEMORY', memory_get_usage(true));     // 开始执行的内存用量

/**
 * 系统常量
 */
define('ROOT', dirname(__DIR__));       // 根目录
define('SEP', DIRECTORY_SEPARATOR);     // 当前系统下的路径分隔符

/**
 * 检查常量 ENV
 */
if (!defined('ENV')) {
    exit('常量 ENV 未定义');
} else {
    if (!in_array(ENV, ['development', 'test', 'production'], true)) {
        exit('常量 ENV 只能定义以下值中的一个: development / test / production');
    }
}

/**
 * 检查常量 MODULE
 */
if (!defined('MODULE')) {
    exit('常量 MODULE 未定义');
} else {
    $moduleDir = ROOT . SEP . 'application' . SEP . 'module' . SEP . MODULE;
    if (!is_dir($moduleDir)) {
        exit('常量 MODULE 指定的应用目录不存在: ' . $moduleDir);
    }
}

/**
 * 时区
 */
date_default_timezone_set('PRC');

/**
 * 开启 session
 */
session_start();

/**
 * 注册自动加载
 */
spl_autoload_register(function($className) {
    $file = ROOT . SEP . str_replace('\\', SEP, $className) . '.php';
    if (!is_file($file)) {
        throw new \Exception('无法加载类文件: ' . $file);
    }
    require_once $file;
    if (!class_exists($className, false) && !interface_exists($className, false)) {
        if (strpos($className, 'Interface') > 0) {
            throw new \Exception('接口未定义: ' . $className);
        } else {
            throw new \Exception('类未定义: ' . $className);
        }
    }
});

/**
 * 运行应用
 */
try{
    library\Application::getInstance()->bootstrap()->run();
} catch (\Exception $e) {
    echo $e->getMessage();
}
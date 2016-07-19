<?php
/**
 * ~start.php
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
 * 错误级别
 */
if (ENV === 'development' || ENV === 'test') {
    error_reporting(E_ALL || E_NOTICE);
    ini_set('display_errors', 1);
} elseif (ENV === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
}

/**
 * 系统常量
 */
define('START_TIME', microtime(true));              // 开始执行的时间
define('START_MEMORY', memory_get_usage(true));     // 开始执行的内存用量
define('ROOT', dirname(__DIR__));                   // 根目录
define('SESSION_SAVE_HANDLER', 'user');             // session.save_handler

/**
 * 时区
 */
date_default_timezone_set('PRC');

/**
 * 加载函数库
 */
$systemFuncFile = ROOT . '/library/common/function.php';
if (!is_file($systemFuncFile)) {
    exit('系统函数库丢失: ' . $systemFuncFile);
}
require $systemFuncFile;

$moduleFuncFile = ROOT . '/application/module/' . MODULE . '/common/function.php';
if (is_file($moduleFuncFile)) {
    require $moduleFuncFile;
}

/**
 * 去掉 PSR-4 autoload，将所有系统类文件合并到 ~runtime.php
 */
$runtimeFile = ROOT . '/library/~runtime.php';
if (!is_file($runtimeFile)) {
    $libraries = array_merge(
        ['Application', 'ControllerAbstract', 'HookInterface', 'Log', 'ModelAbstract', 'Request', 'Response', 'Router', 'Session', 'View'],
        ['database/DatabaseInterface', 'database/Mysqli', 'database/DatabaseFactory'],
        ['exception/ExceptionAbstract', 'exception/DatabaseException', 'exception/FileNotFoundException', 'exception/SystemException']
    );
    $cache = null;
    foreach ($libraries as $file) {
        $file = ROOT . '/library/' . $file . '.php';
        if (!is_file($file)) {
            exit('系统类文件丢失: ' . $file);
        }
        $cache .= file_get_contents($file);
    }
    file_put_contents(
        $runtimeFile,
        '<?php' . preg_replace(['/[ ]+\\r\\n/', '/\\n\\r/'], ['', ''], preg_replace('/\/\*[\w\W]*?\*\//', '', str_replace('<?php', '', $cache)))
    );
    unset($cache);
}
require $runtimeFile;

/**
 * 运行应用
 */
try {
    Application::getInstance()->bootstrap()->run();
} catch (\Exception $e) {
    echo $e->getMessage();
    var_dump($e->getTrace());
    die;
}
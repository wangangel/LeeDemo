<?php
/**
 * ~start.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/9 13:30
 */

/**
 * 版本检查（5.3 开始支持 namespace，5.4 开始支持数组简写和 session_set_save_handler，所以版本限制到 5.4）
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
define('SESSION_CACHE_ENABLE', true);               // 是否开启 cache 存储 SESSION
define('SESSION_CACHE_TIMEOUT', 60 * 60 * 24);      // cache 中 SESSION 的失效时间

/**
 * 时区
 */
date_default_timezone_set('PRC');

/**
 * 去掉 PSR-4 autoload，将 library 中的所有文件合并到 ~runtime.php
 */
$runtimeFile = ROOT . '/library/~runtime.php';
if (!is_file($runtimeFile)) {
    $libraries = array_merge(
        ['cache/CacheInterface', 'cache/MemcacheX'],
        ['common/function'],
        ['database/DatabaseInterface', 'database/MysqliX'],
        ['Application', 'Config', 'ControllerAbstract', 'Log', 'ModelAbstract', 'Request', 'Response', 'Router', 'ServiceAbstract', 'Session', 'View']
    );
    $cache = null;
    foreach ($libraries as $file) {
        $file = ROOT . '/library/' . $file . '.php';
        if (!is_file($file)) {
            exit('系统文件丢失: ' . $file);
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
    // 因为抛错的可能是任何一个对象，所以这里应该都是原生的方法
    if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        exit(json_encode(array(
            'status' => false,
            'message' => $e->getMessage(),
            'code' => $e->getCode()
        )));
    } else {
        exit('<ul><li><h3>' . $e->getMessage() . '</h3>' . str_replace('#', '</li><li>#', $e->getTraceAsString()) . '</li></ul>');
    }
}
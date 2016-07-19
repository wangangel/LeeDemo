<?php
/**
 * function.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/19 9:52
 */

/**
 * 获取配置
 *
 * 使用：C('system') / C('db.master.host')，最多支持到三层
 *
 * 在 /application/config/ 下必须包含系统配置文件
 * 你也可以在 /application/module/[MODULE]/config/ 下定义应用配置文件，相同配置应用的优先级更高
 *
 * @param string $key
 * @return mixed
 * @throws FileNotFoundException
 * @throws SystemException
 */
function C($key)
{
    static $_config = null;

    if ($_config === null) {
        // 系统配置
        $systemConfigFile = ROOT . '/application/config/' . ENV . '.php';
        if (!is_file($systemConfigFile)) {
            throw new FileNotFoundException($systemConfigFile, '系统配置文件丢失');
        }
        $config = include $systemConfigFile;

        // 应用配置
        $moduleConfigFile = ROOT . '/application/module/' . MODULE . '/config/' . ENV . '.php';
        if (is_file($moduleConfigFile)) {
            $config = array_merge(
                $config,
                include $moduleConfigFile
            );
        }

        $_config = $config;
    }

    $ret = null;
    if (strpos($key, '.') > 0) {
        $array = explode('.', $key);
        switch (count($array)) {
            case 2:
                $ret = isset($_config[$array[0]][$array[1]]) ? $_config[$array[0]][$array[1]] : null;
                break;
            case 3:
                $ret = isset($_config[$array[0]][$array[1]][$array[2]]) ? $_config[$array[0]][$array[1]][$array[2]] : null;
                break;
            default:
                break;
        }
    } else {
        $ret = isset($_config[$key]) ? $_config[$key] : null;
    }

    if (is_null($ret)) {
        throw new SystemException('配置不存在: ' . $key);
    }

    return $ret;
}
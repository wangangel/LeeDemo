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
 * @param string $key
 * @return mixed
 */
function C($key)
{
    return Application::getInstance()->getConfigInstance()->get($key);
}

/**
 * 获取模型对象
 *
 * 使用：M('user') 则创建 UserModel
 *
 * @param string $modelName
 * @return ModelAbstract
 */
function M($modelName)
{
    return Application::getInstance()->getModelInstance($modelName);
}

/**
 * $_GET / $_POST / $_REQUEST / $_SERVER / $_FILES / $_ENV / $_COOKIE / $_SESSION
 *
 * 1、$source 决定了从那个全局变量获取：get / post / request / server / files / env / cookie / session
 * 2、参数 $key 不指定则获取该全局变量下的所有值，并且不会设置 $default 默认值和执行 $filter 操作
 * 3、参数 $filter 传入的字符串以 / 开头则认为是正则，否则视为方法
 *
 * @param string $source
 * @param string $key
 * @param mixed $default
 * @param string $filter
 * @return mixed
 */
function I($source, $key = null, $default = null, $filter = null)
{
    return Application::getInstance()->getRequestInstance()->getGlobalVariable($source, $key, $default, $filter);
}
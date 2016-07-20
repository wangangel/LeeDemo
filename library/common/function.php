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
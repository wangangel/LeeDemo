<?php
/**
 * Bootstrap.php
 *
 * User: 670554666@qq.com
 * Date: 2016/8/1 10:53
 */

final class Bootstrap
{
    /**
     * 导入异常错误码配置文件
     */
    public function _initExceptionConfig()
    {
        Application::getInstance()->getConfigInstance()->load(ROOT . '/application/config/exceptionCode.php');
    }
}
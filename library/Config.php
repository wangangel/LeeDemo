<?php
/**
 * Config.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/6 15:25
 */

namespace library;

final class Config
{
    /**
     * 构造器
     *
     * @param array $configArray
     */
    public function __construct($configArray)
    {

    }

    /**
     * 禁止序列化
     */
    private function __sleep() {}

    /**
     * 禁止反序列化
     */
    private function __wakeup() {}

    /**
     * 禁止克隆
     */
    private function __clone() {}
}
<?php
/**
 * DatabaseFactory.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/18 10:33
 */

namespace library\database;

use library\Application;

final class DatabaseFactory
{
    /**
     * @var array 数据库驱动句柄数组（因为可能存在临时切换驱动的情况，比如从 mysql 切换到 mango）
     */
    private static $_driverInstanceArray = [];

    /**
     * 获取数据库驱动
     *
     * @param string $driverName
     * @return DatabaseInterface
     */
    public static function getDriverInstance($driverName = null)
    {
        if ($driverName === null) {
            $driverName = Application::getInstance()->getConfig('db.driver');
        }

        if (!isset(self::$_driverInstanceArray[$driverName])) {
            $class = 'library\\database\\' . $driverName;
            self::$_driverInstanceArray[$driverName] = new $class();
        }

        return self::$_driverInstanceArray[$driverName];
    }
}
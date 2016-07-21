<?php
/**
 * CacheFactory.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/21 14:55
 */

final class CacheFactory
{
    /**
     * @var array 缓存驱动对象数组
     */
    private static $_driverInstanceArray = [];

    /**
     * 获取缓存驱动对象
     *
     * 显式调用该方法可以临时切换缓存驱动
     *
     * @param string $driverName
     * @return CacheInterface
     */
    public static function getDriverInstance($driverName = null)
    {
        $driverName = $driverName === null ? ucfirst(C('cache.driver')) : $driverName;

        if (!isset(self::$_driverInstanceArray[$driverName])) {
            self::$_driverInstanceArray[$driverName] = new $driverName;
        }

        return self::$_driverInstanceArray[$driverName];
    }
}
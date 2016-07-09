<?php
/**
 * G.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/9 10:34
 */

namespace library;

class G
{
    /**
     * @var string 默认模块
     */
    const defaultModule = 'www';

    /**
     * @var string 默认控制器
     */
    const defaultController = 'index';

    /**
     * @var string 默认动作
     */
    const defaultAction = 'index';

    /**
     * @var array 存放的数据
     */
    private static $_data = array();

    /**
     * 存放
     *
     * @param string $key
     * @param mixed $value
     * @param bool $force
     * @return mixed
     * @throws \Exception
     */
    public static function set($key, $value, $force = false)
    {
        if ($force === false && isset(self::$_data[$key])) {
            throw new \Exception('G::set() [' . $key . '] 无法在非强制的情况下覆盖已存在的键值');
        }

        self::$_data[$key] = $value;

        return $value;
    }

    /**
     * 获取
     *
     * @param string $key
     * @return mixed
     */
    public static function get($key)
    {
        if (isset(self::$_data[$key])) {
            return self::$_data[$key];
        } else {
            return null;
        }
    }
}
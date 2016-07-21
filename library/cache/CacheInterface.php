<?php
/**
 * CacheInterface.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/21 14:55
 */

interface CacheInterface
{
    /**
     * 读取
     *
     * @param string $key
     * @return mixed
     */
    public function get($key);

    /**
     * 写入
     *
     * @param string $key
     * @param mixed $value
     * @param int $expiration
     * @return bool
     */
    public function set($key, $value, $expiration);
}
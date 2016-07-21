<?php
/**
 * Memcachedd.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/21 15:06
 */

final class Memcachedd implements CacheInterface
{
    /**
     * @var Memcached 非当前对象，而是 Memcached 句柄
     */
    private $_memcachedInstance = null;

    /**
     * 构造器
     *
     * @throws StorageException
     */
    public function __construct()
    {
        try {
            $memcached = new \Memcached();
            $memcached->addServers(C('cache.servers'));
            $this->_memcachedInstance = $memcached;
        } catch (\MemcachedException $e) {
            throw new StorageException('memcached', $e->getMessage(), $e->getCode());
        }
    }

    /**
     * 读取
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        $value = $this->_memcachedInstance->get($key);
        if ($value === \Memcached::RES_NOTFOUND) {
            return false;
        }

        return $value;
    }

    /**
     * 写入
     *
     * @param string $key
     * @param mixed $value
     * @param int $expiration
     * @return bool
     */
    public function set($key, $value, $expiration = 0)
    {
        return $this->_memcachedInstance->set($key, $value, $expiration);
    }
}
<?php
/**
 * MemcacheX.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/21 15:06
 */

final class MemcacheX implements CacheInterface
{
    /**
     * @var Memcache 非当前对象，而是 Memcache 句柄
     */
    private $_memcacheInstance = null;

    /**
     * 构造器
     *
     * @throws StorageException
     */
    public function __construct()
    {
        try {
            $memcached = new \Memcache();
            $servers = Application::getInstance()->getConfigInstance()->get('cache.servers');
            foreach ($servers as $server) {
                $memcached->addServer($server['HOST'], $server['PORT']);
            }
            $this->_memcacheInstance = $memcached;
        } catch (\MemcachedException $e) {
            throw new StorageException('memcached', $e->getMessage(), $e->getCode());
        }
    }

    /**
     * 读取
     *
     * 返回 key 对应的存储元素的字符串值或者在失败或 key 未找到的时候返回 FALSE
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->_memcacheInstance->get($key);
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
        return $this->_memcacheInstance->set($key, $value, MEMCACHE_COMPRESSED, $expiration);
    }

    /**
     * 删除
     *
     * @param string $key
     * @param int $timeout
     * @return bool
     */
    public function delete($key, $timeout = 0)
    {
        return $this->_memcacheInstance->delete($key, $timeout);
    }
}
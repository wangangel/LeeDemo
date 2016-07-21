<?php
/**
 * Session.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/16 9:22
 */

final class Session implements \SessionHandlerInterface
{
    /**
     * @var CacheInterface|null 缓存对象
     */
    private $_cacheInstance = null;

    /**
     * 构造器
     */
    public function __construct()
    {
        $this->_cacheInstance = Application::getInstance()->getCacheInstance();
    }

    /**
     * 自动开始会话或者通过调用 session_start() 手动开始会话之后第一个被调用的回调函数
     *
     * @param string $savePath
     * @param string $sessionName
     * @return bool
     */
    public function open($savePath, $sessionName)
    {

    }

    public function read($sessionId)
    {

    }

    public function write($sessionId , $sessionData)
    {

    }

    public function close()
    {

    }

    public function destroy($sessionId)
    {

    }

    public function gc($maxLifetime)
    {

    }
}
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
     * open 回调函数类似于类的构造函数，在会话打开的时候会被调用。
     * 这是自动开始会话或者通过调用 session_start() 手动开始会话之后第一个被调用的回调函数。
     * 此回调函数操作成功返回 TRUE，反之返回 FALSE。
     *
     * @param string $savePath
     * @param string $sessionName
     * @return bool
     */
    public function open($savePath, $sessionName)
    {
        return true;
    }

    /**
     * 如果会话中有数据，read 回调函数必须返回将会话数据编码（序列化）后的字符串。如果会话中没有数据，read 回调函数返回空字符串。
     * 在自动开始会话或者通过调用 session_start() 函数手动开始会话之后，PHP 内部调用 read 回调函数来获取会话数据。在调用 read 之前，PHP 会调用 open 回调函数。
     * read 回调返回的序列化之后的字符串格式必须与 write 回调函数保存数据时的格式完全一致。
     * PHP 会自动反序列化返回的字符串并填充 $_SESSION 超级全局变量。
     * 虽然数据看起来和 serialize() 函数很相似，但是需要提醒的是，它们是不同的。
     *
     * @param string $sessionId
     * @return mixed
     */
    public function read($sessionId)
    {
        return $this->_cacheInstance->get('sess-' . $sessionId);
    }

    /**
     * 在会话保存数据时会调用 write 回调函数。此回调函数接收当前会话 ID 以及 $_SESSION 中数据序列化之后的字符串作为参数。
     * 序列化会话数据的过程由 PHP 根据 session.serialize_handler 设定值来完成。
     * 序列化后的数据将和会话 ID 关联在一起进行保存。当调用 read 回调函数获取数据时，所返回的数据必须要和传入 write 回调函数的数据完全保持一致。
     * PHP 会在脚本执行完毕或调用 session_write_close() 函数之后调用此回调函数。
     * 注意，在调用完此回调函数之后，PHP 内部会调用 close 回调函数。
     *
     * Note: PHP 会在输出流写入完毕并且关闭之后才调用 write 回调函数，
     * 所以在 write 回调函数中的调试信息不会输出到浏览器中。
     * 如果需要在 write 回调函数中使用调试输出，建议将调试输出写入到文件。
     *
     * @param string $sessionId
     * @param string $sessionData
     * @return bool
     */
    public function write($sessionId, $sessionData)
    {
        $this->_cacheInstance->set('sess-' . $sessionId, $sessionData, SESSION_CACHE_TIMEOUT);
    }

    /**
     * close 回调函数类似于类的析构函数。在 write 回调函数调用之后调用。
     * 当调用 session_write_close() 函数之后，也会调用 close 回调函数。此回调函数操作成功返回 TRUE，反之返回 FALSE。
     *
     * @return bool
     */
    public function close()
    {
        return true;
    }

    /**
     * 当调用 session_destroy() 函数，或者调用 session_regenerate_id() 函数并且设置 destroy 参数为 TRUE 时，会调用此回调函数。
     * 此回调函数操作成功返回 TRUE，反之返回 FALSE。
     *
     * @param int $sessionId
     * @return bool
     */
    public function destroy($sessionId)
    {
        $this->_cacheInstance->delete('sess-' . $sessionId);
    }

    /**
     * 为了清理会话中的旧数据，PHP 会不时的调用垃圾收集回调函数。
     * 调用周期由 session.gc_probability 和 session.gc_divisor 参数控制。
     * 传入到此回调函数的 lifetime 参数由 session.gc_maxlifetime 设置。
     * 此回调函数操作成功返回 TRUE，反之返回 FALSE。
     *
     * ps: 回收概率 = gc_probability / gc_divisor，由于此项目设置了使用 Session 对象的前提是 gc_probability = 0，所以并不会调用 gc()
     *
     * @param int $maxLifetime
     * @return bool
     */
    public function gc($maxLifetime)
    {
        return false;
    }
}
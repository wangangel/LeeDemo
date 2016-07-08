<?php
/**
 * Request.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/8 16:27
 */

namespace library;

final class Request
{
    /**
     * @var string 请求方式
     */
    private $_method = null;

    /**
     * $_GET
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getGlobalQuery($key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }

        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }

    /**
     * $_POST
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getGlobalPost($key = null, $default = null)
    {
        if ($key === null) {
            return $_POST;
        }

        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }

    /**
     * $_REQUEST
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getGlobalRequest($key = null, $default = null)
    {
        if ($key === null) {
            return $_REQUEST;
        }

        return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $default;
    }

    /**
     * $_SERVER
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getGlobalServer($key = null, $default = null)
    {
        if ($key === null) {
            return $_SERVER;
        }

        return isset($_SERVER[$key]) ? $_SERVER[$key] : $default;
    }

    /**
     * 获取请求方式
     *
     * 可能的值：GET、POST、HEAD、PUT、DELETE、CLI、UNKNOWN
     *
     * @return string
     */
    public function getMethod()
    {
        if ($this->_method === null) {
            $method = $this->getGlobalServer('REQUEST_METHOD');
            if ($method) {
                $this->_method = strtoupper($method);
            } else {
                $sapi = php_sapi_name();
                if (strtolower($sapi) === 'cli' || strtolower(substr($sapi, 0, 3)) === 'cgi') {
                    $this->_method = 'CLI';
                } else {
                    $this->_method = 'UNKNOWN';
                }
            }
        }

        return $this->_method;
    }

    /**
     * 当前请求方式是否是 CLI
     *
     * @return bool
     */
    public function isCli()
    {
        return $this->getMethod() === 'CLI';
    }
}
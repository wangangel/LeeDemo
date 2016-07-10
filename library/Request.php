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
     * @var string 控制器名称
     */
    private $_controllerName = null;

    /**
     * @var string 动作名称
     */
    private $_actionName = null;

    /**
     * @var bool 是否已经路由过
     */
    private $_isRouted = false;

    /**
     * $_GET
     *
     * 1、参数 $key 不指定则不会设置 $default 默认值和执行 $filter 操作
     * 2、参数 $filter 传入的字符串以 / 开头则认为是正则，否则视为方法
     *
     * @param string $key
     * @param mixed $default
     * @param string $filter
     * @return mixed
     */
    public function getGlobalQuery($key = null, $default = null, $filter = null)
    {
        if ($key === null) {
            return $_GET;
        }

        $ret = null;
        if(isset($_GET[$key])){
            $ret = $_GET[$key];
            if ($filter !== null) {
                if (substr($filter, 0, 1) === '/') {
                    if (!preg_match($filter, $ret)) {
                        $ret = $default;
                    }
                } else {
                    $ret = $filter($ret);
                }
            }
        } else {
            $ret = $default;
        }

        return $ret;
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

    /**
     * 设置控制器名称
     *
     * @param string $controllerName
     * @return object
     * @throws \Exception
     */
    public function setControllerName($controllerName)
    {
        if ($this->_isRouted === true) {
            throw new \Exception('当前 Request 对象已经被路由过，无法再重新设置控制器名称');
        }
        $this->_controllerName = $controllerName;

        return $this;
    }

    /**
     * 设置动作名称
     *
     * @param string $actionName
     * @return object
     * @throws \Exception
     */
    public function setActionName($actionName)
    {
        if ($this->_isRouted === true) {
            throw new \Exception('当前 Request 对象已经被路由过，无法再重新设置动作名称');
        }
        $this->_actionName = $actionName;

        return $this;
    }

    /**
     * 设置已经路由过
     *
     * @return object
     */
    public function setRouted()
    {
        $this->_isRouted = true;

        return $this;
    }

    /**
     * 获取控制器名称
     *
     * @return string
     */
    public function getControllerName()
    {
        return $this->_controllerName;
    }

    /**
     * 获取动作名称
     *
     * @return string
     */
    public function getActionName()
    {
        return $this->_actionName;
    }

    /**
     * 获取是否路由过
     *
     * @return bool
     */
    public function getRouted()
    {
        return $this->_isRouted;
    }
}
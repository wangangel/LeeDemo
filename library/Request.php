<?php
/**
 * Request.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/8 16:27
 */

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
     * $_GET / $_POST / $_REQUEST / $_SERVER / $_FILES / $_ENV / $_COOKIE / $_SESSION
     *
     * 1、$source 决定了从那个全局变量获取：get / post / request / server / files / env / cookie / session
     * 2、参数 $key 不指定则获取该全局变量下的所有值，并且不会设置 $default 默认值和执行 $filter 操作
     *
     * @param string $source
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getGlobalVariable($source, $key = null, $default = null)
    {
        if (!in_array(strtolower($source), ['get', 'post', 'request', 'server', 'files', 'env', 'cookie', 'session'])) {
            return null;
        }

        $data = null;
        eval('$data = $_' . strtoupper($source) . ';');

        if ($key === null) {
            return $data;
        }

        return isset($data[$key]) ? $data[$key] : $default;
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
            $method = $this->getGlobalVariable('server', 'REQUEST_METHOD');
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
     * 是否是 ajax 请求
     *
     * @return bool
     */
    public function isAjax()
    {
        return strtolower($this->getGlobalVariable('server', 'HTTP_X_REQUESTED_WITH')) === 'ajax';
    }

    /**
     * 设置控制器名称
     *
     * 原先有 $isRouted 协助判断是否路由过来禁止路由之后再次修改 controllerName
     * 现在去掉这个限制，这样可以在 afterRoute Hook 中再次修改来定位到其它的 Controller，提供了一定的自由度
     *
     * @param string $controllerName
     * @return object
     * @throws \Exception
     */
    public function setControllerName($controllerName)
    {
        $this->_controllerName = $controllerName;

        return $this;
    }

    /**
     * 设置动作名称
     *
     * $isRouted 判断已去除，原因同上
     *
     * @param string $actionName
     * @return object
     * @throws \Exception
     */
    public function setActionName($actionName)
    {
        $this->_actionName = $actionName;

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
}
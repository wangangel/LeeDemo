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
     * 3、参数 $filter 传入的字符串以 / 开头则认为是正则，否则视为方法，多个方法以 | 分割，多个参数以 , 分割，# 代指原值，比如：
     *      'htmlspecialchars|substr,#,0,6|urlencode' => urlencode(substr(htmlspecialchars($value), 0, 6))
     *
     * @param string $source
     * @param string $key
     * @param mixed $default
     * @param string $filter
     * @return mixed
     */
    public function getGlobalVariable($source, $key = null, $default = null, $filter = null)
    {
        if (!in_array(strtolower($source), ['get', 'post', 'request', 'server', 'files', 'env', 'cookie', 'session'])) {
            return null;
        }

        $data = $GLOBALS['_' . strtoupper($source)];

        if ($key === null) {
            return $data;
        }

        $ret = null;
        if(isset($data[$key])){
            $ret = $data[$key];
            if ($filter !== null) {
                if (substr($filter, 0, 1) === '/') {
                    if (!preg_match($filter, $ret)) {
                        $ret = $default;
                    }
                } else {
                    $functions = explode('|', $filter);
                    $value = null;
                    foreach ($functions as $func) {
                        if (strpos($func, ',')) {
                            $exps = explode(',', $func);
                            $func = array_shift($exps);


                            // todo


                        } else {
                            $value = $func($ret);
                        }
                    }
                    $ret = $value;
                }
            }
        } else {
            $ret = $default;
        }

        return $ret;
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
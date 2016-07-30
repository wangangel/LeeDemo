<?php
/**
 * Response.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/8 16:27
 */

final class Response
{
    /**
     * @var array 响应头数组
     */
    private $_headerArray = [];

    /**
     * @var string 响应的内容
     */
    private $_body = null;

    /**
     * 构造器
     */
    public function __construct()
    {
        // 正常渲染视图的请求会使用这个默认的响应头
        // ControllerAbstract 中的 json() / image() 会使用自己覆盖后的响应头，redirect() 的 Location 在这之后，所以不会存在跳转不了的问题
        $this->setHeader('Content-Type', 'text/html;charset=UTF-8', true, 200);
    }

    /**
     * 设置响应头
     *
     * 可选参数 replace 表明是否用后面的头替换前面相同类型的头。 默认情况下会替换。如果传入 FALSE，就可以强制使相同的头信息并存。例如：
     * header('WWW-Authenticate: Negotiate');
     * header('WWW-Authenticate: NTLM', false);
     *
     * $replace === true 则强制替换掉前一个同名的 header
     *
     * @param string $name
     * @param string $value
     * @param bool $replace
     * @param int $code
     */
    public function setHeader($name, $value, $replace = true, $code = 0)
    {
        if ($replace === true) {
            foreach ($this->_headerArray as $k => $v) {
                if ($v['name'] === $name) {
                    unset($this->_headerArray[$k]);
                }
            }
        }

        $this->_headerArray[] = [
            'name' => $name,
            'value' => $value,
            'replace' => $replace,
            'code' => $code
        ];
    }

    /**
     * 设置响应的内容
     *
     * @param string $content
     */
    public function setBody($content)
    {
        $this->_body = $content;
    }

    /**
     * 输出响应头
     */
    public function sendHeaders()
    {
        foreach ($this->_headerArray as $header) {
            if ($header['code'] !== 0) {
                header(
                    $header['name'] . ':' . $header['value'],
                    $header['replace'],
                    $header['code']
                );
            } else {
                header(
                    $header['name'] . ':' . $header['value'],
                    $header['replace']
                );
            }
        }
    }

    /**
     * 执行响应
     */
    public function output()
    {
        $this->sendHeaders();
        exit($this->_body);
    }
}
<?php
/**
 * View.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/12 21:06
 */

final class View
{
    /**
     * @var string 视图路径
     */
    private $_viewPath = null;

    /**
     * 设置视图路径
     *
     * @param string $viewPath
     * @return View
     */
    public function setViewPath($viewPath)
    {
        $this->_viewPath = $viewPath;
        return $this;
    }

    /**
     * 渲染视图并返回渲染后的数据
     *
     * @param string $viewFileName
     * @param array $data
     * @return string
     * @throws Exception
     */
    public function render($viewFileName, $data)
    {
        extract($data);
        ob_start();
        $viewFile = $this->_viewPath . '/' . $viewFileName;
        if (!is_file($viewFile)) {
            throw new \Exception($viewFile, 10009);
        }
        require $viewFile;
        $ret = ob_get_clean();

        return $ret;
    }
}
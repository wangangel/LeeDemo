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
     * 构造器
     */
    public function __construct()
    {
        // 暂时默认视图到这里（目前不提供 setViewPath()，也不支持第三方如 smarty / volt 等模版引擎，将来可扩展）
        $this->_viewPath = ROOT . '/application/module/' . MODULE . '/view/' . Application::getInstance()->getRequestInstance()->getControllerName();
    }

    /**
     * 渲染视图并返回渲染后的数据
     *
     * @param string $viewFileName
     * @param array $data
     * @return string
     * @throws FileNotFoundException
     */
    public function render($viewFileName, $data)
    {
        extract($data);
        ob_start();
        $viewFile = $this->_viewPath . '/' . $viewFileName;
        if (!is_file($viewFile)) {
            throw new FileNotFoundException($viewFile, '视图文件丢失');
        }
        include($viewFile);
        $ret = ob_get_clean();

        return $ret;
    }
}
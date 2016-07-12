<?php
/**
 * View.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/12 21:06
 */

namespace library;

class View
{
    /**
     * @var string 视图路径
     */
    private $_tplPath = '';

    /**
     * @var array 视图数据
     */
    private $_tplData = array();

    /**
     * 设置视图路径
     *
     * @param string $path
     */
    public function setTplPath($path)
    {
        $this->_tplPath = $path;
    }

    /**
     * 获取视图路径
     *
     * @return string
     */
    public function getTplPath()
    {
        return $this->_tplPath;
    }
}
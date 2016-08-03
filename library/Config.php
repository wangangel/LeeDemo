<?php
/**
 * Config.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/20 13:18
 */

final class Config
{
    /**
     * @var array|null 配置数组
     */
    private $_configArray = null;

    /**
     * 导入配置
     *
     * load(ROOT . 'application/config/exceptionCode.php') / load(['asd' => 123])
     *
     * @param mixed $source
     * @throws Exception
     */
    public function load($source)
    {
        $config = null;
        if (is_string($source)) {
            if (!is_file($source)) {
                throw new \Exception($source, 10007);
            }
            $config = include $source;
        } elseif (is_array($source)) {
            $config = $source;
        } else {
            throw new \Exception($source, 10018);
        }

        if ($config !== null) {
            if ($this->_configArray === null) {
                $this->_configArray = $config;
            } else {
                $this->_configArray = array_merge($this->_configArray, $config);
            }
        }
    }

    /**
     * 获取配置
     *
     * get('system') / get('db.master.host')，最多支持到三层
     *
     * @param string $key
     * @return mixed
     * @throws Exception
     */
    public function get($key = null)
    {
        $ret = null;
        if ($key === null) {
            $ret = $this->_configArray;
        } elseif (strpos($key, '.') > 0) {
            $array = explode('.', $key);
            switch (count($array)) {
                case 2:
                    $ret = $this->_configArray[$array[0]][$array[1]] === null ? null : $this->_configArray[$array[0]][$array[1]];
                    break;
                case 3:
                    $ret = $this->_configArray[$array[0]][$array[1]][$array[2]] === null ? null : $this->_configArray[$array[0]][$array[1]][$array[2]];
                    break;
                default:
                    break;
            }
        } else {
            $ret = $this->_configArray[$key] === null ? null : $this->_configArray[$key];
        }

        if ($ret === null) {
            throw new \Exception($key, 10008);
        }

        return $ret;
    }
}
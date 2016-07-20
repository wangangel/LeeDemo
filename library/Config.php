<?php
/**
 * Config.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/20 13:18
 */

class Config
{
    /**
     * @var array|null 配置数组
     */
    private $_configArray = null;

    /**
     * 构造器
     *
     * 如果传递了配置数组，则按配置数组配置
     * 否则按默认规则加载配置文件：/application/config，/application/module/MODULE/config
     *
     * @param array $configArray
     * @throws FileNotFoundException
     * @throws UndefinedException
     */
    public function __construct($configArray = null)
    {
        if ($configArray !== null) {
            $this->_configArray = $configArray;
        } else {
            // 系统配置
            $systemConfigFile = ROOT . '/application/config/' . ENV . '.php';
            if (!is_file($systemConfigFile)) {
                throw new FileNotFoundException($systemConfigFile, '系统配置文件丢失');
            }
            $config = include $systemConfigFile;

            // 应用配置
            $moduleConfigFile = ROOT . '/application/module/' . MODULE . '/config/' . ENV . '.php';
            if (is_file($moduleConfigFile)) {
                $config = array_merge(
                    $config,
                    include $moduleConfigFile
                );
            }

            $this->_configArray = $config;
        }
    }

    /**
     * 获取配置
     *
     * get('system') / get('db.master.host')，最多支持到三层
     *
     * @param string $key
     * @return mixed
     * @throws UndefinedException
     */
    public function get($key)
    {
        $ret = null;
        if (strpos($key, '.') > 0) {
            $array = explode('.', $key);
            switch (count($array)) {
                case 2:
                    $ret = isset($this->_configArray[$array[0]][$array[1]]) ? $this->_configArray[$array[0]][$array[1]] : null;
                    break;
                case 3:
                    $ret = isset($this->_configArray[$array[0]][$array[1]][$array[2]]) ? $this->_configArray[$array[0]][$array[1]][$array[2]] : null;
                    break;
                default:
                    break;
            }
        } else {
            $ret = isset($this->_configArray[$key]) ? $this->_configArray[$key] : null;
        }

        if (is_null($ret)) {
            throw new UndefinedException('config', $key, '配置不存在');
        }

        return $ret;
    }
}
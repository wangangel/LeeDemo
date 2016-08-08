<?php
/**
 * ThemeModel.php
 *
 * User: 670554666@qq.com
 * Date: 2016/8/4 14:16
 */

class ThemeModel extends ModelAbstract
{
    /**
     * 主题状态
     */
    const STATUS_VERIFY = 1;    // 待审核
    const STATUS_NORMAL = 2;    // 正常
    const STATUS_DELETE = 3;    // 已删除

    /**
     * @var string 表名
     */
    protected $_tableName = 'theme';

    /**
     * 根据 id 获取主题
     *
     * @param int $themeId
     * @return mixed
     */
    public function getById($themeId)
    {
        $theme = $this->_databaseInstance
            ->table($this->_tableName)
            ->where('id', 'eq', $themeId)
            ->limit(1)
            ->select();

        return $theme !== false && !empty($theme) ? $theme[0] : $theme;
    }
}
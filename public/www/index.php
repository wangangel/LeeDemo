<?php
/**
 * index.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/5 18:42
 */

/**
 * 加载 Application.php
 */
require dirname(dirname(__DIR__)) . '/library/Application.php';

/**
 * 执行
 */
(new library\Application())->run();
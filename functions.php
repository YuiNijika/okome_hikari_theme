<?php

/**
 * 主题核心文件
 * Theme core file
 * @link https://github.com/YuiNijika/TTDF
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// 加载框架核心文件
$ttdfFrameworkPath = __DIR__ . '/core/Main.php';
if (file_exists($ttdfFrameworkPath)) {
    require $ttdfFrameworkPath;
} else {
    throw new Exception('TTDF核心文件加载失败, 请检查框架是否安装完整: ' . $ttdfFrameworkPath);
}

require_once __DIR__ . '/core/Widget/shortcode.php';

/**
 * 主题自定义代码
 * theme custom code
 */
// 自定义文章编辑器按钮
Typecho_Plugin::factory('admin/write-post.php')->bottom = array('Editor', 'edit');
Typecho_Plugin::factory('admin/write-page.php')->bottom = array('Editor', 'edit');
class Editor
{
    public static function edit()
    {
        echo "<script src='" . Helper::options()->themeUrl . '/assets/editor.js' . "'></script>";
    }
}

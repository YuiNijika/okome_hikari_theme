<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
// 定义字段配置
return [
    [
        // Text
        'type' => 'Text',
        'name' => 'FeaturedImage',
        'value' => '', // 默认值为空字符串
        'label' => '特色图片',
        'description' => '显示在文章头部的特色图片~',
        'attributes' => [
            'style' => 'width: 100%;' // 自定义样式
        ]
    ],
];

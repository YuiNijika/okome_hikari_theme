<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * 主题设置
 * Html可以使用element组件
 * https://element-plus.org/zh-CN/component/overview
 */
return [
    'Rice-Options' => [
        'title' => '主题设置',
        'fields' => [
            [
                'type' => 'Text',
                'name' => 'sideBarImg',
                'value' => '#',
                'label' => '侧边栏图片',
                'description' => '侧边栏图片'
            ],
            [
                'type' => 'Text',
                'name' => 'sideBarDesc',
                'value' => '你好呀~',
                'label' => '侧边栏描述文本',
                'description' => '侧边栏描述文本'
            ],
            [
                'type' => 'Text',
                'name' => 'friends',
                'value' => '#',
                'label' => '好友列表',
                'description' => '好友列表链接'
            ],
            [
                'type' => 'Text',
                'name' => 'aboutLink',
                'value' => '#',
                'label' => '关于页面',
                'description' => '关于页面链接'
            ],
            [
                'type' => 'Text',
                'name' => 'loadingImg',
                'value' => '#',
                'label' => '加载背景图',
                'description' => '设置图像加载的占位图'
            ],
            [
                'type' => 'Text',
                'name' => 'MetingApiUrl',
                'value' => '#',
                'label' => 'MetingApiUrl',
                'description' => '设置MetingApiUrl'
            ],
            [
                'type' => 'Textarea',
                'name' => 'pjax_Content',
                'value' => '',
                'label' => 'PJAX回调函数',
                'description' => 'PJAX回调函数，用于在PJAX加载完成后执行'
            ],
        ]
    ],
    'Color-Options' => [
        'title' => '颜色设置',
        'fields' => [
            [
                'type' => 'ColorPicker',
                'name' => 'theme_color',
                'value' => '',
                'label' => '主题颜色',
                'description' => '选择网站的主题颜色，支持十六进制颜色值输入。'
            ],
            [
                'type' => 'ColorPicker',
                'name' => 'neutral_color',
                'value' => '',
                'label' => '中性色',
                'description' => '设置站点的中性色（Neutral）网站背景颜色、卡片颜色等。'
            ],
            [
                'type' => 'ColorPicker',
                'name' => 'secondary_color',
                'value' => '',
                'label' => '次要颜色',
                'description' => '设置站点的次要颜色（Secondary）。'
            ],
            [
                'type' => 'ColorPicker',
                'name' => 'accent_color',
                'value' => '',
                'label' => '强调颜色',
                'description' => '设置站点的强调颜色（Accent）。'
            ],

            [
                'type' => 'ColorPicker',
                'name' => 'info_color',
                'value' => '',
                'label' => '信息颜色',
                'description' => '设置站点的信息颜色（Info）。'
            ],
            [
                'type' => 'ColorPicker',
                'name' => 'success_color',
                'value' => '',
                'label' => '成功颜色',
                'description' => '设置站点的成功颜色（Success）。'
            ],
            [
                'type' => 'ColorPicker',
                'name' => 'warning_color',
                'value' => '',
                'label' => '警告颜色',
                'description' => '设置站点的警告颜色（Warning）。'
            ],
            [
                'type' => 'ColorPicker',
                'name' => 'error_color',
                'value' => '',
                'label' => '错误颜色',
                'description' => '设置站点的错误颜色（Error）。'
            ],
            [
                'type' => 'Radio',
                'name' => 'color_intensity',
                'value' => 'medium',
                'label' => '主题色调强度',
                'description' => '控制浅色/深色底色的浓淡程度',
                'layout' => 'vertical',
                'options' => [
                    'soft' => '更淡',
                    'medium' => '适中',
                    'bold' => '更浓'
                ]
            ],
            [
                'type' => 'Radio',
                'name' => 'border_radius',
                'value' => '0.5rem',
                'label' => '全局圆角',
                'description' => '选择全局圆角大小（影响卡片、输入框等）',
                'layout' => 'vertical',
                'options' => [
                    '0rem' => '0rem',
                    '0.25rem' => '0.25rem',
                    '0.5rem' => '0.5rem',
                    '1rem' => '1rem',
                    '2rem' => '2rem'
                ]
            ],
        ]
    ],

];

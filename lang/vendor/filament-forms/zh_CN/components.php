<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => '克隆',
            ],

            'add' => [

                'label' => '添加到 :label',

                'modal' => [

                    'heading' => '添加到 :label',

                    'actions' => [

                        'add' => [
                            'label' => '添加',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => '在块之间插入',

                'modal' => [

                    'heading' => '添加到 :label',

                    'actions' => [

                        'add' => [
                            'label' => '添加',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => '删除',
            ],

            'edit' => [

                'label' => '编辑',

                'modal' => [

                    'heading' => '编辑区块',

                    'actions' => [

                        'save' => [
                            'label' => '保存更改',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => '移动',
            ],

            'move_down' => [
                'label' => '下移',
            ],

            'move_up' => [
                'label' => '上移',
            ],

            'collapse' => [
                'label' => '收起',
            ],

            'expand' => [
                'label' => '展开',
            ],

            'collapse_all' => [
                'label' => '全部收起',
            ],

            'expand_all' => [
                'label' => '全部展开',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => '取消全选',
            ],

            'select_all' => [
                'label' => '全选',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => '取消',
                ],

                'drag_crop' => [
                    'label' => '拖动模式“裁剪”',
                ],

                'drag_move' => [
                    'label' => '拖动模式“移动”',
                ],

                'flip_horizontal' => [
                    'label' => '水平翻转图像',
                ],

                'flip_vertical' => [
                    'label' => '垂直翻转图像',
                ],

                'move_down' => [
                    'label' => '向下移动图像',
                ],

                'move_left' => [
                    'label' => '将图像移动到左侧',
                ],

                'move_right' => [
                    'label' => '将图像移动到右侧',
                ],

                'move_up' => [
                    'label' => '向上移动图像',
                ],

                'reset' => [
                    'label' => '重置',
                ],

                'rotate_left' => [
                    'label' => '将图像向左旋转',
                ],

                'rotate_right' => [
                    'label' => '将图像向右旋转',
                ],

                'set_aspect_ratio' => [
                    'label' => '将纵横比设置为 :ratio',
                ],

                'save' => [
                    'label' => '保存',
                ],

                'zoom_100' => [
                    'label' => '将图像缩放到100%',
                ],

                'zoom_in' => [
                    'label' => '放大',
                ],

                'zoom_out' => [
                    'label' => '缩小',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => '高度',
                    'unit' => '像素',
                ],

                'rotation' => [
                    'label' => '旋转',
                    'unit' => '度',
                ],

                'width' => [
                    'label' => '宽度',
                    'unit' => '像素',
                ],

                'x_position' => [
                    'label' => 'X',
                    'unit' => '像素',
                ],

                'y_position' => [
                    'label' => 'Y',
                    'unit' => '像素',
                ],

            ],

            'aspect_ratios' => [

                'label' => '纵横比',

                'no_fixed' => [
                    'label' => '自由',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => '不建议编辑 SVG 文件，因为缩放时可能导致质量损失。\n 您确定要继续吗？',
                    'disabled' => '已禁用 SVG 文件编辑功能，因为缩放时可能导致质量损失。',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => '添加行',
            ],

            'delete' => [
                'label' => '删除行',
            ],

            'reorder' => [
                'label' => '重新排序行',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => '键名',
            ],

            'value' => [
                'label' => '值',
            ],

        ],

    ],

    'markdown_editor' => [

        'tools' => [
            'attach_files' => '附件',
            'blockquote' => '引用',
            'bold' => '加粗',
            'bullet_list' => '普通列表',
            'code_block' => '代码',
            'heading' => '标题',
            'italic' => '斜体',
            'link' => '链接',
            'ordered_list' => '数字列表',
            'redo' => '重做',
            'strike' => '删除线',
            'table' => '表格',
            'undo' => '撤销',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => '选择',

                'actions' => [

                    'select' => [
                        'label' => '选择',
                    ],

                ],

            ],

        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => '是',
            'false' => '否',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => '添加 :label',
            ],

            'add_between' => [
                'label' => '在中间插入',
            ],

            'delete' => [
                'label' => '删除',
            ],

            'clone' => [
                'label' => '克隆',
            ],

            'reorder' => [
                'label' => '移动',
            ],

            'move_down' => [
                'label' => '下移',
            ],

            'move_up' => [
                'label' => '上移',
            ],

            'collapse' => [
                'label' => '收起',
            ],

            'expand' => [
                'label' => '展开',
            ],

            'collapse_all' => [
                'label' => '全部收起',
            ],

            'expand_all' => [
                'label' => '全部展开',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => '上传文件',

                'modal' => [

                    'heading' => '上传文件',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => '文件',
                                'existing' => '替换文件',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => '提示文本',
                                'existing' => '更改提示文本',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => '插入',
                        ],

                        'save' => [
                            'label' => '保存',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => '编辑链接',

                'modal' => [

                    'heading' => '链接',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => '在新窗口打开',
                        ],

                    ],

                ],

            ],

        ],

        'no_merge_tag_search_results_message' => '没有找到合并标签结果。',

        'tools' => [
            'align_center' => '居中对齐',
            'align_end' => '右对齐',
            'align_justify' => '两端对齐',
            'align_start' => '左对齐',
            'attach_files' => '附件',
            'blockquote' => '引用',
            'bold' => '加粗',
            'bullet_list' => '普通列表',
            'clear_formatting' => '清除格式',
            'code' => '代码',
            'code_block' => '代码块',
            'custom_blocks' => '自定义区块',
            'details' => '详情',
            'h1' => '标题',
            'h2' => '标题',
            'h3' => '副标题',
            'highlight' => '高亮',
            'horizontal_rule' => '水平线',
            'italic' => '斜体',
            'lead' => '引导文本',
            'link' => '链接',
            'merge_tags' => '合并标签',
            'ordered_list' => '数字列表',
            'redo' => '重做',
            'small' => '小号文字',
            'strike' => '删除线',
            'subscript' => '下标',
            'superscript' => '上标',
            'table' => '表格',
            'table_delete' => '删除表格',
            'table_add_column_before' => '在前面添加列',
            'table_add_column_after' => '在后面添加列',
            'table_delete_column' => '删除列',
            'table_add_row_before' => '在上方添加行',
            'table_add_row_after' => '在下方添加行',
            'table_delete_row' => '删除行',
            'table_merge_cells' => '合并单元格',
            'table_split_cell' => '拆分单元格',
            'table_toggle_header_row' => '切换标题行',
            'underline' => '下划线',
            'undo' => '撤销',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => '创建',

                'modal' => [

                    'heading' => '创建',

                    'actions' => [

                        'create' => [
                            'label' => '创建',
                        ],

                        'create_another' => [
                            'label' => '创建并继续创建',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => '编辑',

                'modal' => [

                    'heading' => '编辑',

                    'actions' => [

                        'save' => [
                            'label' => '保存',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => '是',
            'false' => '否',
        ],

        'loading_message' => '加载中...',

        'max_items_message' => '只能选择 :count 个。',

        'no_search_results_message' => '没有选项匹配您的搜索',

        'placeholder' => '选择选项',

        'searching_message' => '搜索中...',

        'search_prompt' => '输入内容以搜索...',

    ],

    'tags_input' => [
        'placeholder' => '新标签',
    ],

    'text_input' => [

        'actions' => [

            'hide_password' => [
                'label' => '隐藏密码',
            ],

            'show_password' => [
                'label' => '显示密码',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => '是',
            'false' => '否',
        ],

    ],

];

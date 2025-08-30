<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => '身份验证器应用',

            'below_content' => '使用安全应用生成临时代码进行登录验证。',

            'messages' => [
                'enabled' => '已启用',
                'disabled' => '已禁用',
            ],

        ],

    ],

    'login_form' => [

        'label' => '使用身份验证器应用中的代码',

        'code' => [

            'label' => '输入身份验证器应用中的6位代码',

            'validation_attribute' => '代码',

            'actions' => [

                'use_recovery_code' => [
                    'label' => '改用恢复代码',
                ],

            ],

            'messages' => [

                'invalid' => '您输入的代码无效。',

            ],

        ],

        'recovery_code' => [

            'label' => '或者，输入恢复代码',

            'validation_attribute' => '恢复代码',

            'messages' => [

                'invalid' => '您输入的恢复代码无效。',

            ],

        ],

    ],

];

<?php

return [

    'label' => '关闭',

    'modal' => [

        'heading' => '禁用身份验证器应用',

        'description' => '您确定要停止使用身份验证器应用吗？禁用此功能将从您的账户中移除额外的安全层。',

        'form' => [

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

        'actions' => [

            'submit' => [
                'label' => '禁用身份验证器应用',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => '身份验证器应用已被禁用',
        ],

    ],

];

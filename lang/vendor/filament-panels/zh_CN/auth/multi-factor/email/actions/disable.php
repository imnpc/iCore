<?php

return [

    'label' => '关闭',

    'modal' => [

        'heading' => '禁用邮箱验证码',

        'description' => '您确定要停止接收邮箱验证码吗？禁用此功能将从您的账户中移除额外的安全层。',

        'form' => [

            'code' => [

                'label' => '输入我们通过邮箱发送给您的6位代码',

                'validation_attribute' => '代码',

                'actions' => [

                    'resend' => [

                        'label' => '通过邮箱发送新代码',

                        'notifications' => [

                            'resent' => [
                                'title' => '我们已通过邮箱向您发送了新代码',
                            ],

                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => '您输入的代码无效。',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => '禁用邮箱验证码',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => '邮箱验证码已被禁用',
        ],

    ],

];

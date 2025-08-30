<?php

return [

    'label' => '设置',

    'modal' => [

        'heading' => '设置邮箱验证码',

        'description' => '每次登录或执行敏感操作时，您都需要输入我们通过邮箱发送给您的6位代码。请检查您的邮箱获取6位代码以完成设置。',

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
                'label' => '启用邮箱验证码',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => '邮箱验证码已启用',
        ],

    ],

];

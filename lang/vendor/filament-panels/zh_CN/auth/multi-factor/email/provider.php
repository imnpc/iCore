<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => '邮箱验证码',

            'below_content' => '在登录期间接收发送到您邮箱地址的临时代码以验证您的身份。',

            'messages' => [
                'enabled' => '已启用',
                'disabled' => '已禁用',
            ],

        ],

    ],

    'login_form' => [

        'label' => '发送代码到您的邮箱',

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

];

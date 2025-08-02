<?php

return [

    'title' => '登录',

    'heading' => '登录',

    'actions' => [

        'register' => [
            'before' => '或者',
            'label' => '注册账号',
        ],

        'request_password_reset' => [
            'label' => '忘记了密码？',
        ],

    ],

    'form' => [

        'email' => [
            'label' => '邮箱地址',
        ],

        'password' => [
            'label' => '密码',
        ],

        'remember' => [
            'label' => '保持登录状态',
        ],

        'actions' => [

            'authenticate' => [
                'label' => '登录',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'Verify your identity',

        'subheading' => 'To continue signing in, you need to verify your identity.',

        'form' => [

            'provider' => [
                'label' => 'How would you like to verify?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Confirm sign in',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => '登录信息有误。',

    ],

    'notifications' => [

        'throttled' => [
            'title' => '尝试登录次数过多',
            'body' => '请在 :seconds 秒后重试。',
        ],

    ],

];

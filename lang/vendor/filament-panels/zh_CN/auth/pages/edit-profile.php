<?php

return [

    'label' => '个人资料',

    'form' => [

        'email' => [
            'label' => '邮箱地址',
        ],

        'name' => [
            'label' => '姓名',
        ],

        'password' => [
            'label' => '新密码',
        ],

        'password_confirmation' => [
            'label' => '确认新密码',
        ],

        'current_password' => [
            'label' => '当前密码',
            'below_content' => '为了安全起见，请确认您的密码以继续。',
            'validation_attribute' => '当前密码',
        ],

        'actions' => [

            'save' => [
                'label' => '保存',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => '双重认证 (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => '邮箱地址更改请求已发送',
            'body' => '更改邮箱地址的请求已发送至 :email。请检查您的邮箱以验证更改。',
        ],

        'saved' => [
            'title' => '已保存',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => '取消',
        ],

    ],

];

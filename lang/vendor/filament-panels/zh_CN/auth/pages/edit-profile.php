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
            'label' => 'Current password',
            'below_content' => 'For security, please confirm your password to continue.',
            'validation_attribute' => 'current password',
        ],

        'actions' => [

            'save' => [
                'label' => '保存',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Two-factor authentication (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Email address change request sent',
            'body' => 'A request to change your email address has been sent to :email. Please check your email to verify the change.',
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

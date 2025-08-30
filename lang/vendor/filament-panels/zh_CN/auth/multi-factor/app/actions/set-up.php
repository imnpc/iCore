<?php

return [

    'label' => '设置',

    'modal' => [

        'heading' => '设置身份验证器应用',

        'description' => <<<'BLADE'
            您需要一个类似Google Authenticator的应用（<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>）来完成此过程。
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => '使用您的身份验证器应用扫描此二维码：',

                'alt' => '使用身份验证器应用扫描的二维码',

            ],

            'text_code' => [

                'instruction' => '或者手动输入此代码：',

                'messages' => [
                    'copied' => '已复制',
                ],

            ],

            'recovery_codes' => [

                'instruction' => '请将以下恢复代码保存在安全的地方。它们只会显示一次，但如果您的身份验证器应用无法访问，您将需要这些代码：',

            ],

        ],

        'form' => [

            'code' => [

                'label' => '输入身份验证器应用中的6位代码',

                'validation_attribute' => '代码',

                'below_content' => '每次登录或执行敏感操作时，您都需要输入身份验证器应用中的6位代码。',

                'messages' => [

                    'invalid' => '您输入的代码无效。',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => '启用身份验证器应用',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => '身份验证器应用已启用',
        ],

    ],

];

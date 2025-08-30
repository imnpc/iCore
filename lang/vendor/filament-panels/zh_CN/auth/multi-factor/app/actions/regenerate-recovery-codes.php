<?php

return [

    'label' => '重新生成恢复代码',

    'modal' => [

        'heading' => '重新生成身份验证器应用恢复代码',

        'description' => '如果您丢失了恢复代码，可以在此重新生成。您的旧恢复代码将立即失效。',

        'form' => [

            'code' => [

                'label' => '输入身份验证器应用中的6位代码',

                'validation_attribute' => '代码',

                'messages' => [

                    'invalid' => '您输入的代码无效。',

                ],

            ],

            'password' => [

                'label' => '或者，输入您的当前密码',

                'validation_attribute' => '密码',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => '重新生成恢复代码',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => '新的身份验证器应用恢复代码已生成',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => '新恢复代码',

            'description' => '请将以下恢复代码保存在安全的地方。它们只会显示一次，但如果您的身份验证器应用无法访问，您将需要这些代码：',

            'actions' => [

                'submit' => [
                    'label' => '关闭',
                ],

            ],

        ],

    ],

];

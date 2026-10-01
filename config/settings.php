<?php

declare(strict_types=1);

use Outerweb\Settings\Models\Setting;

return [
    'model' => Setting::class,
    'database' => [
        'table' => 'settings',
    ],
    'api' => [
        'public_keys' => [
            'app',
            'alipay.is_enabled',
            'wechat.is_enabled',
        ],
    ],
];

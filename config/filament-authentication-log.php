<?php

use App\Models\User;

return [
    // 'user-resource' => \App\Filament\Resources\UserResource::class,
    'resources' => [
//        'AutenticationLogResource' => AuthenticationLogResource::class,
        'AutenticationLogResource' => \App\Filament\Clusters\User\Resources\AuthenticationLogResource::class,
    ],

    'authenticable-resources' => [
        User::class,
        'App\Models\Admin',
    ],

    'authenticatable' => [
        'field-to-display' => 'name',
    ],

    'navigation' => [
        'authentication-log' => [
            'register' => true,
            'sort' => 10,
            'icon' => 'heroicon-o-shield-check',
            // 'group' => 'Logins',
        ],
    ],

    'sort' => [
        'column' => 'login_at',
        'direction' => 'desc',
    ],
];

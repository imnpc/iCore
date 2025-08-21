<?php

namespace App\Filament\Clusters\User\Resources\AuthenticationLogs\Pages;

use Filament\Resources\Pages\ListRecords;

class ListAuthenticationLogs extends ListRecords
{
    public static function getResource(): string
    {
        return config('filament-authentication-log.resources.AutenticationLogResource', \Tapp\FilamentAuthenticationLog\Resources\AuthenticationLogResource::class);
    }
}

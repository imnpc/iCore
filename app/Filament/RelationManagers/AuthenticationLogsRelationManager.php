<?php

namespace App\Filament\RelationManagers;

use App\Filament\Resources\AuthenticationLogs\AuthenticationLogResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AuthenticationLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'authentications';

    protected static ?string $recordTitleAttribute = 'id';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return trans('filament-authentication-log::filament-authentication-log.table.heading');
    }

    public function table(Table $table): Table
    {
        return AuthenticationLogResource::table($table);
    }

    protected function canCreate(): bool
    {
        return false;
    }

    protected function canEdit(Model $record): bool
    {
        return false;
    }

    protected function canDelete(Model $record): bool
    {
        return false;
    }
}

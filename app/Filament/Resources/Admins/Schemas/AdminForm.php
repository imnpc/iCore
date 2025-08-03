<?php

namespace App\Filament\Resources\Admins\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AdminForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(),
                TextInput::make('avatar'),
                TextInput::make('mobile'),
                TextInput::make('status')
                    ->required()
                    ->numeric()
                    ->default(1),
                Textarea::make('secret')
                    ->columnSpanFull(),
                Textarea::make('recovery_codes')
                    ->columnSpanFull(),
            ]);
    }
}

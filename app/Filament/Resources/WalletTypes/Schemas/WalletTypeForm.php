<?php

namespace App\Filament\Resources\WalletTypes\Schemas;

use App\Models\WalletType;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class WalletTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(trans('filament-model.general.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label(WalletType::transAttribute('slug'))
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->alpha('ascii')
                    ->maxLength(255)
                    ->helperText("大写英文,创建以后不可修改")
                    ->disabled(fn ($operation) => $operation === 'edit'),
                TextInput::make('description')
                    ->label(WalletType::transAttribute('description'))
                    ->maxLength(255),
                TextInput::make('decimal_places')
                    ->label(WalletType::transAttribute('decimal_places'))
                    ->required()
                    ->numeric()
                    ->helperText("创建以后不可修改")
                    ->default(2)
                    ->disabled(fn ($operation) => $operation === 'edit'),
                FileUpload::make('icon')
                    ->label(WalletType::transAttribute('icon'))
                    ->directory('wallet')
                    ->avatar()
                    ->image()
                    ->columnSpan('full'),
                TextInput::make('sort')
                    ->label(trans('filament-model.general.sort'))
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_enabled')
                    ->label(trans('filament-model.general.is_enabled'))
                    ->required()
                    ->inline(false)
                    ->default(0),
            ]);
    }
}

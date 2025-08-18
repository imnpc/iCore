<?php

namespace App\Filament\Clusters\Settings\Resources\Admins\Schemas;

use App\Models\Admin;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class AdminForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(trans('filament-model.general.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label(trans('filament-model.general.email'))
                    ->email()
                    ->maxLength(255),
//                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->label(trans('filament-model.general.password'))
                    ->password()
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->helperText(Admin::transAttribute('password_help'))
                    ->autocomplete('new-password')
                    ->revealable(),
                PhoneInput::make('mobile')
                    ->label(trans('filament-model.general.mobile'))
                    ->rules(['phone'])
                    ->defaultCountry('CN')
                    ->onlyCountries(['CN'])
                    ->disallowDropdown()
                    ->countryStatePath('phone_country')
                    ->displayNumberFormat(PhoneInputNumberType::NATIONAL),
                FileUpload::make('avatar')
                    ->label(trans('filament-model.general.avatar'))
                    ->directory('avatar/' . date('Y/m/d'))
                    ->avatar()
                    ->image()
                    ->visibility('public')
                    ->columnSpan('full'),
                Select::make('roles')
                    ->label(__('filament-shield::filament-shield.resource.label.role'))
                    ->relationship('roles', 'name')
                    ->preload(),
                Toggle::make('status')
                    ->label(trans('filament-model.general.status'))
                    ->required()
                    ->inline(false)
                    ->default(1),
                TextInput::make('created_at')
                    ->label(trans('filament-model.general.created_at'))
                    ->disabled(),
                TextInput::make('updated_at')
                    ->label(trans('filament-model.general.updated_at'))
                    ->disabled(),
                TextInput::make('banned_at')
                    ->label(trans('filament-model.general.banned_at'))
                    ->disabled(),
            ]);
    }
}

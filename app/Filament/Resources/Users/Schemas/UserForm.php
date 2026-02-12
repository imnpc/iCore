<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use DiscoveryDesign\FilamentGaze\Forms\Components\GazeBanner;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                GazeBanner::make('user')
                    ->lock()
                    ->canTakeControl(fn () => auth()->user()->isAdmin())
                    ->hideOnCreate()
                    ->columnSpan('full'),
                TextInput::make('name')
                    ->label(trans('filament-model.general.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label(trans('filament-model.general.email'))
                    ->email()
                    ->maxLength(255),
                PhoneInput::make('mobile')
                    ->label(trans('filament-model.general.mobile'))
                    ->rules(['phone'])
                    ->defaultCountry('CN')
                    ->onlyCountries(['CN'])
                    ->countryStatePath('phone_country')
                    ->displayNumberFormat(PhoneInputNumberType::NATIONAL),
                TextInput::make('password')
                    ->label(trans('filament-model.general.password'))
                    ->password()
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->helperText(User::transAttribute('password_help'))
                    ->autocomplete('new-password')
                    ->revealable(),
                Toggle::make('status')
                    ->label(trans('filament-model.general.status'))
                    ->onColor('success')
                    ->offColor('danger')
                    ->onIcon(Heroicon::OutlinedCheck)
                    ->offIcon(Heroicon::OutlinedXMark)
                    ->required()
                    ->inline(false)
                    ->default(1)
                    ->columnSpan('full'),
                TextInput::make('parent_id')
                    ->label(trans('filament-model.general.parent_id'))
                    ->disabled(),
                TextInput::make('created_at')
                    ->label(trans('filament-model.general.created_at'))
                    ->disabled(),
                TextInput::make('updated_at')
                    ->label(trans('filament-model.general.updated_at'))
                    ->disabled(),
                TextInput::make('banned_at')
                    ->label(trans('filament-model.general.banned_at'))
                    ->disabled(),
                SpatieTagsInput::make('tags')
                    ->label(trans('filament-model.general.tags'))
                    ->helperText(trans('filament-model.general.tags_help')),
            ]);
    }
}

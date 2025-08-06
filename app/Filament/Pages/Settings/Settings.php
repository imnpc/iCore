<?php

namespace App\Filament\Pages\Settings;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Closure;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use BackedEnum;

class Settings extends BaseSettings
{
    use HasPageShield;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';
    public function schema(): array|Closure
    {
        return [
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('app')
                            ->label(trans('filament-model.settings.app.title'))
                            ->icon('heroicon-o-computer-desktop')
                            ->schema(self::getAppFields()),
                        Tabs\Tab::make('payment')
                            ->label(trans('filament-model.payment.name'))
                            ->icon('fab-paypal')
                            ->schema(self::getPaymentFields()),
                        Tabs\Tab::make('Tab 3')
                            ->schema([
                                // ...
                            ]),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->activeTab(1),
            ];
    }

    public function getAppFields(): array
    {
        return [
            Section::make(__('filament-model.settings.app.title'))
                ->description(__('filament-model.settings.app.description'))
                ->icon("fab-app-store")
                ->iconColor('primary') // 蓝色
                ->schema([
                    TextInput::make('app.name')
                        ->label(__('filament-model.settings.app.name'))
                        ->maxLength(255)
                        ->required()
                        ->columnSpanFull(),
                    Grid::make()->schema([
                        FileUpload::make('app.logo')
                            ->label(fn() => __('filament-model.settings.app.logo'))
                            ->image()
                            ->directory('assets')
                            ->visibility('public')
                            ->moveFiles()
                            ->imageEditor()
                            ->getUploadedFileNameForStorageUsing(fn() => 'site_logo.png'),
                        FileUpload::make('app.favicon')
                            ->label(fn() => __('filament-model.settings.app.favicon'))
                            ->image()
                            ->directory('assets')
                            ->visibility('public')
                            ->moveFiles()
                            ->getUploadedFileNameForStorageUsing(fn() => 'site_favicon.ico')
                            ->acceptedFileTypes(['image/x-icon', 'image/vnd.microsoft.icon']),
                    ])->columns(3),
                    TextInput::make('app.support_email')
                        ->label(__('filament-model.settings.app.support.email'))
                        ->prefixIcon('heroicon-o-envelope'),
                    PhoneInput::make('app.support_phone')
                        ->label(__('filament-model.settings.app.support.phone'))
                        ->rules(['phone'])
                        ->onlyCountries(['cn'])
                        ->countryStatePath('phone_country')
                        ->disallowDropdown()
                        ->displayNumberFormat(PhoneInputNumberType::NATIONAL),
                    TextInput::make('app.copyright')
                        ->label(__('filament-model.settings.app.copyright'))
                        ->maxLength(255)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ];
    }

    public function getPaymentFields(): array
    {
        return [
            Section::make(__('filament-model.payment.channel.alipay'))
                ->description(__('filament-model.payment.alipay.description'))
                ->icon("fab-alipay")
                ->iconColor('primary') // 蓝色
                ->schema([
                    Toggle::make('alipay.is_enabled')
                        ->label(__('filament-model.payment.enabled'))
                        ->onIcon('fas-check')
                        ->offIcon('fas-xmark')
                        ->onColor('primary')
                        ->offColor('danger')
                        ->inline(false)
                        ->live(true),
//                    TextInput::make('alipay.app_id')
//                        ->label(__('filament-model.payment.alipay.app_id'))
//                        ->maxWidth('sm')
//                        ->required(fn(Get $get) => $get('alipay.is_enabled'))
//                        ->visible(fn(Get $get) => $get('alipay.is_enabled'))
//                        ->columnSpanFull(),
//                    Textarea::make('alipay.app_secret_cert')
//                        ->label(__('filament-model.payment.alipay.app_secret_cert'))
//                        ->required(fn(Get $get) => $get('alipay.is_enabled'))
//                        ->visible(fn(Get $get) => $get('alipay.is_enabled'))
//                        ->columnSpanFull(),
//                    Textarea::make('alipay.app_public_cert_path')
//                        ->label(__('filament-model.payment.alipay.app_public_cert_path'))
//                        ->required(fn(Get $get) => $get('alipay.is_enabled'))
//                        ->visible(fn(Get $get) => $get('alipay.is_enabled'))
//                        ->columnSpanFull(),
//                    FileUpload::make('alipay.alipay_public_cert_path')
//                        ->label(__('filament-model.payment.alipay.alipay_public_cert_path'))
//                        ->required(fn(Get $get) => $get('alipay.is_enabled'))
//                        ->visible(fn(Get $get) => $get('alipay.is_enabled'))
//                        ->helperText('alipayCertPublicKey_RSA2.crt')
//                        ->directory('payment/alipay')
//                        ->maxWidth('sm'),
//                    FileUpload::make('alipay.alipay_root_cert_path')
//                        ->label(__('filament-model.payment.alipay.alipay_root_cert_path'))
//                        ->required(fn(Get $get) => $get('alipay.is_enabled'))
//                        ->visible(fn(Get $get) => $get('alipay.is_enabled'))
//                        ->helperText('alipayRootCert.crt')
//                        ->directory('payment/alipay')
//                        ->maxWidth('sm'),
//                    Radio::make('alipay.mode')
//                        ->label(__('filament-model.payment.alipay.mode'))
//                        ->required(fn(Get $get) => $get('alipay.is_enabled'))
//                        ->visible(fn(Get $get) => $get('alipay.is_enabled'))
//                        ->options([
//                            Pay::MODE_NORMAL  => __('filament-model.payment.mode.normal'),
//                            Pay::MODE_SANDBOX => __('filament-model.payment.mode.sandbox'),
//                            Pay::MODE_SERVICE => __('filament-model.payment.mode.service'),
//                        ])
//                        ->default(0)
//                        ->maxWidth('sm')
//                        ->inline()
//                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->collapsed()
                ->persistCollapsed()
                ->id('settings-payment-alipay'),
            Section::make(trans('filament-model.payment.channel.wechat'))
                ->description(__('filament-model.payment.wechat.description'))
                ->icon("fab-weixin")
                ->iconColor('success') // 蓝色
                ->schema([
                    Toggle::make('wechat.is_enabled')
                        ->label(__('filament-model.payment.enabled'))
                        ->onIcon('fas-check')
                        ->offIcon('fas-xmark')
                        ->onColor('success')
                        ->offColor('danger')
                        ->inline(false)
                        ->live(),
//                    TextInput::make('wechat.mch_id')
//                        ->label(__('filament-model.payment.wechat.mch_id'))
//                        ->required(fn(Get $get) => $get('wechat.is_enabled'))
//                        ->visible(fn(Get $get) => $get('wechat.is_enabled'))
//                        ->maxWidth('sm')
//                        ->columnSpanFull(),
//                    TextInput::make('wechat.mch_secret_key')
//                        ->label(__('filament-model.payment.wechat.mch_secret_key'))
//                        ->required(fn(Get $get) => $get('wechat.is_enabled'))
//                        ->visible(fn(Get $get) => $get('wechat.is_enabled'))
//                        ->maxWidth('sm')
//                        ->columnSpanFull(),
//                    FileUpload::make('wechat.mch_secret_cert')
//                        ->label(__('filament-model.payment.wechat.mch_secret_cert'))
//                        ->required(fn(Get $get) => $get('wechat.is_enabled'))
//                        ->visible(fn(Get $get) => $get('wechat.is_enabled'))
//                        ->helperText('apiclient_key.pem')
//                        ->directory('payment/wechat')
//                        ->maxWidth('sm'),
//                    FileUpload::make('wechat.mch_public_cert_path')
//                        ->label(__('filament-model.payment.wechat.mch_public_cert_path'))
//                        ->required(fn(Get $get) => $get('wechat.is_enabled'))
//                        ->visible(fn(Get $get) => $get('wechat.is_enabled'))
//                        ->helperText('apiclient_cert.pem')
//                        ->directory('payment/wechat')
//                        ->maxWidth('sm'),
//
//                    TextInput::make('wechat.mp_app_id')
//                        ->label(__('filament-model.payment.wechat.mp_app_id'))
//                        ->visible(fn(Get $get) => $get('wechat.is_enabled'))
//                        ->maxWidth('sm')
//                        ->columnSpanFull(),
//                    TextInput::make('wechat.mini_app_id')
//                        ->label(__('filament-model.payment.wechat.mini_app_id'))
//                        ->visible(fn(Get $get) => $get('wechat.is_enabled'))
//                        ->maxWidth('sm'),
//                    TextInput::make('wechat.app_id')
//                        ->label(__('filament-model.payment.wechat.app_id'))
//                        ->visible(fn(Get $get) => $get('wechat.is_enabled'))
//                        ->maxWidth('sm')
//                        ->columnSpanFull(),
//                    Select::make('wechat.mode')
//                        ->label(__('filament-model.payment.wechat.mode'))
//                        ->required(fn(Get $get) => $get('wechat.is_enabled'))
//                        ->visible(fn(Get $get) => $get('wechat.is_enabled'))
//                        ->options([
//                            Pay::MODE_NORMAL  => __('filament-model.payment.mode.normal'),
//                            Pay::MODE_SERVICE => __('filament-model.payment.mode.service'),
//                        ])
//                        ->default(Pay::MODE_NORMAL)
//                        ->maxWidth('sm')
//                        ->columnSpanFull(),

                ])
                ->columns(2)
                ->collapsed()
                ->persistCollapsed()
                ->id('settings-payment-wechat'),
        ];
    }


    public function getTitle(): string
    {
        return __('filament-model.settings.general.name');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament-model.settings.general.name');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-model.settings.general.name');
    }
}

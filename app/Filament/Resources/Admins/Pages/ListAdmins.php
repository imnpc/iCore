<?php

namespace App\Filament\Resources\Admins\Pages;

use App\Filament\Resources\Admins\AdminResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAdmins extends ListRecords
{
    protected static string $resource = AdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    /**
     * 标签过滤
     * @return array|Tab[]
     */
    public function getTabs(): array
    {
        $modelClass = static::getModel();

        return [
            'all' => Tab::make('all')
                ->label(trans('filament-model.general.all'))
                ->icon('heroicon-o-shield-check')
                ->badge($modelClass::query()->count()),
            'active' => Tab::make('Active')
                ->label(trans('filament-model.general.active'))
                ->icon('heroicon-o-shield-check')
                ->badgeColor('success')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', true))
                ->badge($modelClass::query()->where('status', true)->count()),
            'inactive' => Tab::make('Inactive')
                ->label(trans('filament-model.general.inactive'))
                ->icon('heroicon-o-x-circle')
                ->badgeColor('danger')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', false))
                ->badge($modelClass::query()->where('status', false)->count()),
        ];
    }
}

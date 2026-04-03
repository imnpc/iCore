<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Permission\Resources\Roles\Pages;

use App\Filament\Clusters\Permission\Resources\RoleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

/**
 * 角色列表页面。
 */
class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

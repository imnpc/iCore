<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Permission\Resources\Roles\Pages;

use App\Filament\Clusters\Permission\Resources\RoleResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    protected function getActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

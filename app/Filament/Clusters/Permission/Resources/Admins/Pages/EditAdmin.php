<?php

namespace App\Filament\Clusters\Permission\Resources\Admins\Pages;

use App\Filament\Clusters\Permission\Resources\AdminResource;
use Filament\Resources\Pages\EditRecord;

class EditAdmin extends EditRecord
{
    protected static string $resource = AdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            DeleteAction::make()->visible(fn ($record): bool => $record->id > 1),
//            ForceDeleteAction::make(),
//            RestoreAction::make(),
        ];
    }
}

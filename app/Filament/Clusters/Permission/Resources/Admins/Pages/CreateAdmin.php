<?php

namespace App\Filament\Clusters\Permission\Resources\Admins\Pages;

use App\Filament\Clusters\Permission\Resources\AdminResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAdmin extends CreateRecord
{
    protected static string $resource = AdminResource::class;
}

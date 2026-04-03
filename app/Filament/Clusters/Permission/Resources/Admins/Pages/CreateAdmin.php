<?php

namespace App\Filament\Clusters\Permission\Resources\Admins\Pages;

use App\Filament\Clusters\Permission\Resources\AdminResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * 管理员创建页面。
 */
class CreateAdmin extends CreateRecord
{
    protected static string $resource = AdminResource::class;
}

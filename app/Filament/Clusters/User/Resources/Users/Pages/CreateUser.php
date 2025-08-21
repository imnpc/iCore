<?php

namespace App\Filament\Clusters\User\Resources\Users\Pages;

use App\Filament\Clusters\User\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}

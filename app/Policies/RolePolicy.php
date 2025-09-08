<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('viewAny_Role');
    }

    public function view(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('view_Role');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_Role');
    }

    public function update(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('update_Role');
    }

    public function delete(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('delete_Role');
    }

}
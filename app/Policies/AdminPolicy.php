<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('viewAny_Admin');
    }

    public function view(AuthUser $authUser): bool
    {
        return $authUser->can('view_Admin');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_Admin');
    }

    public function update(AuthUser $authUser): bool
    {
        return $authUser->can('update_Admin');
    }

    public function delete(AuthUser $authUser): bool
    {
        return $authUser->can('delete_Admin');
    }

    public function restore(AuthUser $authUser): bool
    {
        return $authUser->can('restore_Admin');
    }

    public function forceDelete(AuthUser $authUser): bool
    {
        return $authUser->can('forceDelete_Admin');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('forceDeleteAny_Admin');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restoreAny_Admin');
    }

    public function replicate(AuthUser $authUser): bool
    {
        return $authUser->can('replicate_Admin');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_Admin');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('deleteAny_Admin');
    }

}
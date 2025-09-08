<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('viewAny_User');
    }

    public function view(AuthUser $authUser): bool
    {
        return $authUser->can('view_User');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_User');
    }

    public function update(AuthUser $authUser): bool
    {
        return $authUser->can('update_User');
    }

    public function delete(AuthUser $authUser): bool
    {
        return $authUser->can('delete_User');
    }

    public function restore(AuthUser $authUser): bool
    {
        return $authUser->can('restore_User');
    }

    public function forceDelete(AuthUser $authUser): bool
    {
        return $authUser->can('forceDelete_User');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('forceDeleteAny_User');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restoreAny_User');
    }

    public function replicate(AuthUser $authUser): bool
    {
        return $authUser->can('replicate_User');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_User');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('deleteAny_User');
    }

    /**
     * 是否本人
     * @param User $user
     * @return bool
     */
    public function own(User $user)
    {
        return $user->id === auth()->id();
    }

}
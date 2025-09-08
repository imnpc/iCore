<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class AuthenticationLogPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('viewAny_AuthenticationLog');
    }

    public function view(AuthUser $authUser, AuthenticationLog $authenticationLog): bool
    {
        return $authUser->can('view_AuthenticationLog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_AuthenticationLog');
    }

    public function update(AuthUser $authUser, AuthenticationLog $authenticationLog): bool
    {
        return $authUser->can('update_AuthenticationLog');
    }

    public function delete(AuthUser $authUser, AuthenticationLog $authenticationLog): bool
    {
        return $authUser->can('delete_AuthenticationLog');
    }

    public function restore(AuthUser $authUser, AuthenticationLog $authenticationLog): bool
    {
        return $authUser->can('restore_AuthenticationLog');
    }

    public function forceDelete(AuthUser $authUser, AuthenticationLog $authenticationLog): bool
    {
        return $authUser->can('forceDelete_AuthenticationLog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('forceDeleteAny_AuthenticationLog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restoreAny_AuthenticationLog');
    }

    public function replicate(AuthUser $authUser, AuthenticationLog $authenticationLog): bool
    {
        return $authUser->can('replicate_AuthenticationLog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_AuthenticationLog');
    }

}
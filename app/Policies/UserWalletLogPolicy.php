<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\UserWalletLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserWalletLogPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('viewAny_UserWalletLog');
    }

    public function view(AuthUser $authUser, UserWalletLog $userWalletLog): bool
    {
        return $authUser->can('view_UserWalletLog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_UserWalletLog');
    }

    public function update(AuthUser $authUser, UserWalletLog $userWalletLog): bool
    {
        return $authUser->can('update_UserWalletLog');
    }

    public function delete(AuthUser $authUser, UserWalletLog $userWalletLog): bool
    {
        return $authUser->can('delete_UserWalletLog');
    }

    public function restore(AuthUser $authUser, UserWalletLog $userWalletLog): bool
    {
        return $authUser->can('restore_UserWalletLog');
    }

    public function forceDelete(AuthUser $authUser, UserWalletLog $userWalletLog): bool
    {
        return $authUser->can('forceDelete_UserWalletLog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('forceDeleteAny_UserWalletLog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restoreAny_UserWalletLog');
    }

    public function replicate(AuthUser $authUser, UserWalletLog $userWalletLog): bool
    {
        return $authUser->can('replicate_UserWalletLog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_UserWalletLog');
    }

    /**
     * 验证是否属于本人
     *
     * @param User $user
     * @param UserWalletLog $userWalletLog
     * @return bool
     */
    public function own(User $user, UserWalletLog $userWalletLog): bool
    {
        return $user->id === $userWalletLog->user_id;
    }
}
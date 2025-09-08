<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\WalletType;
use Illuminate\Auth\Access\HandlesAuthorization;

class WalletTypePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('viewAny_WalletType');
    }

    public function view(AuthUser $authUser, WalletType $walletType): bool
    {
        return $authUser->can('view_WalletType');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_WalletType');
    }

    public function update(AuthUser $authUser, WalletType $walletType): bool
    {
        return $authUser->can('update_WalletType');
    }

    public function delete(AuthUser $authUser, WalletType $walletType): bool
    {
        return $authUser->can('delete_WalletType');
    }

    public function restore(AuthUser $authUser, WalletType $walletType): bool
    {
        return $authUser->can('restore_WalletType');
    }

    public function forceDelete(AuthUser $authUser, WalletType $walletType): bool
    {
        return $authUser->can('forceDelete_WalletType');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('forceDeleteAny_WalletType');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restoreAny_WalletType');
    }

    public function replicate(AuthUser $authUser, WalletType $walletType): bool
    {
        return $authUser->can('replicate_WalletType');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_WalletType');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('deleteAny_WalletType');
    }

}
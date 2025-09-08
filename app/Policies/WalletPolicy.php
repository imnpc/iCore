<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use TomatoPHP\FilamentWallet\Models\Wallet;
use Illuminate\Auth\Access\HandlesAuthorization;

class WalletPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('viewAny_Wallet');
    }

    public function view(AuthUser $authUser, Wallet $wallet): bool
    {
        return $authUser->can('view_Wallet');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_Wallet');
    }

    public function update(AuthUser $authUser, Wallet $wallet): bool
    {
        return $authUser->can('update_Wallet');
    }

    public function delete(AuthUser $authUser, Wallet $wallet): bool
    {
        return $authUser->can('delete_Wallet');
    }

    public function restore(AuthUser $authUser, Wallet $wallet): bool
    {
        return $authUser->can('restore_Wallet');
    }

    public function forceDelete(AuthUser $authUser, Wallet $wallet): bool
    {
        return $authUser->can('forceDelete_Wallet');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('forceDeleteAny_Wallet');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restoreAny_Wallet');
    }

    public function replicate(AuthUser $authUser, Wallet $wallet): bool
    {
        return $authUser->can('replicate_Wallet');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_Wallet');
    }

}
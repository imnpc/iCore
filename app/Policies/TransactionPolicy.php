<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use TomatoPHP\FilamentWallet\Models\Transaction;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('viewAny_Transaction');
    }

    public function view(AuthUser $authUser, Transaction $transaction): bool
    {
        return $authUser->can('view_Transaction');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_Transaction');
    }

    public function update(AuthUser $authUser, Transaction $transaction): bool
    {
        return $authUser->can('update_Transaction');
    }

    public function delete(AuthUser $authUser, Transaction $transaction): bool
    {
        return $authUser->can('delete_Transaction');
    }

    public function restore(AuthUser $authUser, Transaction $transaction): bool
    {
        return $authUser->can('restore_Transaction');
    }

    public function forceDelete(AuthUser $authUser, Transaction $transaction): bool
    {
        return $authUser->can('forceDelete_Transaction');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('forceDeleteAny_Transaction');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restoreAny_Transaction');
    }

    public function replicate(AuthUser $authUser, Transaction $transaction): bool
    {
        return $authUser->can('replicate_Transaction');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_Transaction');
    }

}
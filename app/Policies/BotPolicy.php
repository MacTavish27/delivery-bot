<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Bot;
use Illuminate\Auth\Access\HandlesAuthorization;

class BotPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Bot');
    }

    public function view(AuthUser $authUser, Bot $bot): bool
    {
        return $authUser->can('View:Bot');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Bot');
    }

    public function update(AuthUser $authUser, Bot $bot): bool
    {
        return $authUser->can('Update:Bot');
    }

    public function delete(AuthUser $authUser, Bot $bot): bool
    {
        return $authUser->can('Delete:Bot');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Bot');
    }

    public function restore(AuthUser $authUser, Bot $bot): bool
    {
        return $authUser->can('Restore:Bot');
    }

    public function forceDelete(AuthUser $authUser, Bot $bot): bool
    {
        return $authUser->can('ForceDelete:Bot');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Bot');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Bot');
    }

    public function replicate(AuthUser $authUser, Bot $bot): bool
    {
        return $authUser->can('Replicate:Bot');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Bot');
    }

}
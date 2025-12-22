<?php

namespace Modules\Organization\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Organization\Models\Organization;
use Modules\User\Models\User;

class OrganizationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Organization');
    }

    public function view(User $user, Organization $record): bool
    {
        return $user->can('View:Organization');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Organization');
    }

    public function update(User $user, Organization $record): bool
    {
        return $user->can('Update:Organization');
    }

    public function delete(User $user, Organization $record): bool
    {
        return $user->can('Delete:Organization');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('Delete:Organization');
    }

    public function restore(User $user, Organization $record): bool
    {
        return $user->can('Restore:Organization');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Organization');
    }

    public function forceDelete(User $user, Organization $record): bool
    {
        return $user->can('ForceDelete:Organization');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Organization');
    }

    public function replicate(User $user, Organization $record): bool
    {
        return $user->can('replicate_organization');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_organization');
    }
}

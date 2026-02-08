<?php

namespace Modules\Workspace\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Workspace\Models\Workspace;
use Modules\User\Models\User;

class WorkspacePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Workspace');
    }

    public function view(User $user, Workspace $record): bool
    {
        return $user->can('View:Workspace');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Workspace');
    }

    public function update(User $user, Workspace $record): bool
    {
        return $user->can('Update:Workspace');
    }

    public function delete(User $user, Workspace $record): bool
    {
        return $user->can('Delete:Workspace');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('Delete:Workspace');
    }

    public function restore(User $user, Workspace $record): bool
    {
        return $user->can('Restore:Workspace');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Workspace');
    }

    public function forceDelete(User $user, Workspace $record): bool
    {
        return $user->can('ForceDelete:Workspace');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Workspace');
    }

    public function replicate(User $user, Workspace $record): bool
    {
        return $user->can('replicate_workspace');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_workspace');
    }
}

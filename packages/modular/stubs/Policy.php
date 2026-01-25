<?php

namespace StubModuleNamespace\StubSubModulePrefix\Policies;


use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\User\Models\User;

use StubModuleNamespace\StubSubModulePrefix\Models\StubTableName;

class StubTableNamePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:StubTableName');
    }

    /** Bitta yozuvni ko'rish (view) */
    public function view(User $user, StubTableName $record): bool
    {
        return $user->can('View:StubTableName');
    }

    /** Yaratish (create) */
    public function create(User $user): bool
    {
        return $user->can('Create:StubTableName');
    }

    /** Tahrirlash (update) */
    public function update(User $user, StubTableName $record): bool
    {
        return $user->can('Update:StubTableName');
    }

    /** O'chirish (delete) */
    public function delete(User $user, StubTableName $record): bool
    {
        return $user->can('Delete:StubTableName');
    }

    /** Bulk o‘chirish (Filament bulk actions) */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:StubTableName');
    }

    /** Tiklash (SoftDeletes bo'lsa) */
    public function restore(User $user, StubTableName $record): bool
    {
        return $user->can('Restore:StubTableName');
    }

    /** Majburiy o‘chirish (force delete) */
    public function forceDelete(User $user, StubTableName $record): bool
    {
        return $user->can('ForceDelete:StubTableName');
    }

    /** Bulk force delete */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:StubTableName');
    }

    /** Nusxa olish (replicate) */
    public function replicate(User $user, StubTableName $record): bool
    {
        return $user->can('Replicate:StubTableName');
    }

    /** Qatorlarni tartiblash (reorder) */
    public function reorder(User $user): bool
    {
        return $user->can('Reorder:StubTableName');
    }

    /** Bulk restore */
    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:StubTableName');
    }
}
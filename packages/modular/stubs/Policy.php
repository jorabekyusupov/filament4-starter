<?php

namespace StubModuleNamespace\StubSubModulePrefix\Policies;


namespace StubModuleNamespace\StubSubModulePrefix\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\User\Models\User;

use StubModuleNamespace\StubSubModulePrefix\Models\StubTableName;

class StubTableNamePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('viewAny_stubTableName');
    }

    /** Bitta yozuvni ko'rish (view) */
    public function view(User $user, StubTableName $record): bool
    {
        return $user->can('view_stubTableName');
    }

    /** Yaratish (create) */
    public function create(User $user): bool
    {
        return $user->can('create_stubTableName');
    }

    /** Tahrirlash (update) */
    public function update(User $user, StubTableName $record): bool
    {
        return $user->can('update_stubTableName');
    }

    /** O'chirish (delete) */
    public function delete(User $user, StubTableName $record): bool
    {
        return $user->can('delete_stubTableName');
    }

    /** Bulk o‘chirish (Filament bulk actions) */
    public function deleteAny(User $user): bool
    {
        return $user->can('deleteAny_stubTableName');
    }

    /** Tiklash (SoftDeletes bo'lsa) */
    public function restore(User $user, StubTableName $record): bool
    {
        return $user->can('restore_stubTableName');
    }

    /** Majburiy o‘chirish (force delete) */
    public function forceDelete(User $user, StubTableName $record): bool
    {
        return $user->can('forceDelete_stubTableName');
    }

    /** Bulk force delete */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('forceDeleteAny_stubTableName');
    }

    /** Nusxa olish (replicate) */
    public function replicate(User $user, StubTableName $record): bool
    {
        return $user->can('replicate_stubTableName');
    }

    /** Qatorlarni tartiblash (reorder) */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_stubTableName');
    }

    /** Bulk restore */
    public function restoreAny(User $user): bool
    {
        return $user->can('restoreAny_stubTableName');
    }
}
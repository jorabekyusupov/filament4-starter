<?php

namespace Modules\Order\Policies;


namespace Modules\Order\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\User\Models\User;

use Modules\Order\Models\Order;

class OrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('viewAny_orders');
    }

    /** Bitta yozuvni ko'rish (view) */
    public function view(User $user, Order $record): bool
    {
        return $user->can('view_orders');
    }

    /** Yaratish (create) */
    public function create(User $user): bool
    {
        return $user->can('create_orders');
    }

    /** Tahrirlash (update) */
    public function update(User $user, Order $record): bool
    {
        return $user->can('update_orders');
    }

    /** O'chirish (delete) */
    public function delete(User $user, Order $record): bool
    {
        return $user->can('delete_orders');
    }

    /** Bulk o‘chirish (Filament bulk actions) */
    public function deleteAny(User $user): bool
    {
        return $user->can('deleteAny_orders');
    }

    /** Tiklash (SoftDeletes bo'lsa) */
    public function restore(User $user, Order $record): bool
    {
        return $user->can('restore_orders');
    }

    /** Majburiy o‘chirish (force delete) */
    public function forceDelete(User $user, Order $record): bool
    {
        return $user->can('forceDelete_orders');
    }

    /** Bulk force delete */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('forceDeleteAny_orders');
    }

    /** Nusxa olish (replicate) */
    public function replicate(User $user, Order $record): bool
    {
        return $user->can('replicate_orders');
    }

    /** Qatorlarni tartiblash (reorder) */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_orders');
    }

    /** Bulk restore */
    public function restoreAny(User $user): bool
    {
        return $user->can('restoreAny_orders');
    }
}
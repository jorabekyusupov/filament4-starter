<?php

namespace Modules\Language\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Language\Models\Language;
use Modules\User\Models\User;

class LanguagePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Language');
    }

    public function view(User $user, Language $language): bool
    {
        return $user->can('View:Language');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Language');
    }

    public function update(User $user, Language $language): bool
    {
        return $user->can('Update:Language');
    }

    public function delete(User $user, Language $language): bool
    {
        return $user->can('Delete:Language');
    }

    public function restore(User $user, Language $language): bool
    {
        return $user->can('Restore:Language');
    }

    public function forceDelete(User $user, Language $language): bool
    {
        return $user->can('ForceDelete:Language');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Language');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Language');
    }
}

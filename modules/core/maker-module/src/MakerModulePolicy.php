<?php

namespace Modules\MakerModule;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Jora\Modular\Console\Commands\Make\MakeModule;
use Modules\MakerModule\Models\Module;
use Modules\User\Models\User;


class MakerModulePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Module');
    }

    public function view(User $user, Module $module): bool
    {
        return $user->can('View:Module');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Module');
    }

    public function update(User $user, Module $module): bool
    {
        return $user->can('Update:Module');
    }

    public function delete(User $user, Module $module): bool
    {
        return $user->can('Delete:Module');
    }

    public function restore(User $user, Module $module): bool
    {
        return $user->can('Restore:Module');
    }

    public function forceDelete(User $user, Module $module): bool
    {
        return $user->can('ForceDelete:Module');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Module');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Module');
    }
}

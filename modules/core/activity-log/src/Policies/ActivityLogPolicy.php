<?php

namespace Modules\ActivityLog\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\User\Models\User;
use Spatie\Activitylog\Models\Activity;

class ActivityLogPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return  $user->can('ViewAny:CustomActivityModel');
    }

    public function view(User $user, Activity $activity): bool
    {
        return  $user->can('View:CustomActivityModel');
    }

    public function create(User $user): bool
    {
        return  $user->can('Create:CustomActivityModel');
    }

    public function update(User $user, Activity $activity): bool
    {
        return  $user->can('Update:CustomActivityModel');
    }

    public function delete(User $user, Activity $activity): bool
    {
        return  $user->can('Delete:CustomActivityModel');
    }

    public function restore(User $user, Activity $activity): bool
    {
        return  $user->can('Restore:CustomActivityModel');
    }

    public function forceDelete(User $user, Activity $activity): bool
    {
        return  $user->can('ForceDelete:CustomActivityModel');
    }
}

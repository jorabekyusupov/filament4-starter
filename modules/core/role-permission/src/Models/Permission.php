<?php

namespace Modules\RolePermission\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Modules\RolePermission\Observers\PermissionObserver;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[ObservedBy([PermissionObserver::class])]
class Permission extends \Spatie\Permission\Models\Permission
{
    use LogsActivity;
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll();
    }
    protected $fillable = [
        'name',
        'guard_name',
        'translations',
        'group',
        'module',
        'group_sort',
        'module_sort',
        'sort'
    ];

    protected $casts = [
        'translations' => 'array',
        'group_sort' => 'integer',
        'module_sort' => 'integer',
        'sort' => 'integer',
    ];
}

<?php

namespace Modules\RolePermission\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Modules\RolePermission\Observers\PermissionObserver;

#[ObservedBy([PermissionObserver::class])]
class Permission extends \Spatie\Permission\Models\Permission
{
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

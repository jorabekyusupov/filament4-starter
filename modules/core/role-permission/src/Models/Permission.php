<?php

namespace Modules\RolePermission\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends \Spatie\Permission\Models\Permission
{
    protected $fillable =[
        'name',
        'guard_name',
        'translations',
        'group',
        'module',
        'group_sort',
        'module_sort',
        'sort'
    ];
}

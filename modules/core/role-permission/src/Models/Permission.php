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

    protected $casts = [
        'translations' => 'array',
        'group_sort' => 'integer',
        'module_sort' => 'integer',
        'sort' => 'integer',
    ];
}

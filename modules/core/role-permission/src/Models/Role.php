<?php

namespace Modules\RolePermission\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends \Spatie\Permission\Models\Role
{
    protected $fillable = [
        'name',
        'guard_name',
        'translations',
        'organization_id',
        'sort',
        'is_dont_delete'
    ];

    protected $casts = [
        'translations' => 'array',
        'is_dont_delete' => 'boolean',
        'sort' => 'integer',
    ];
}

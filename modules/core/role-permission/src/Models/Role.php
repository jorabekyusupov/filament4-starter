<?php

namespace Modules\RolePermission\Models;

use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Organization\Models\Organization;
use Modules\RolePermission\Policies\RolePolicy;

#[UsePolicy(RolePolicy::class)]
class Role extends \Spatie\Permission\Models\Role
{
    use SoftDeletes;
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

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }
}

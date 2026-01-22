<?php

namespace Modules\RolePermission\Models;

use App\Policies\RolePolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Modules\Organization\Models\Organization;
use Modules\Organization\Policies\OrganizationPolicy;

#[UsePolicy(RolePolicy::class)]
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

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }
}

<?php

namespace Modules\RolePermission\Models;

use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Workspace\Models\Workspace;
use Modules\RolePermission\Policies\RolePolicy;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[UsePolicy(RolePolicy::class)]
class Role extends \Spatie\Permission\Models\Role
{
    use SoftDeletes,LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll();
    }


    protected $fillable = [
        'name',
        'guard_name',
        'translations',
        'workspace_id',
        'sort',
        'is_dont_delete'
    ];

    protected $casts = [
        'translations' => 'array',
        'is_dont_delete' => 'boolean',
        'sort' => 'integer',
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class, 'workspace_id');
    }
}

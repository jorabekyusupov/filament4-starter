<?php

namespace Modules\Organization\Models;

use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Organization\Policies\OrganizationPolicy;
use Modules\RolePermission\Models\Permission;

#[UsePolicy(OrganizationPolicy::class)]
class Organization extends Model
{
    use SoftDeletes;

    public const SLUG_DEFAULT = 'default';
    public const SLUG_CA = 'ca';

    protected $fillable = [
        'slug',
        'structure_id',
        'hidden',
        'name',
        'status',
        'is_dont_delete',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'hidden' => 'boolean',
            'status' => 'boolean',
            'is_dont_delete' => 'boolean',
        ];
    }



    public function scopeDefaultId(): ?int
    {
        return $this->where('slug', 'default')->value('id');
    }

    public function scopeWithoutHidden()
    {
        return $this
            ->when(!auth()->user()->hasSuperAdmin(), function (Builder $query) {
                $query->where('hidden', false);
            });
    }

    public function users()
    {
        return $this->hasMany(\Modules\User\Models\User::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'organization_permissions',
            'organization_id',
            'permission_id'
        );
    }

}

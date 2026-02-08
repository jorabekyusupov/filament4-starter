<?php

namespace Modules\Workspace\Models;

use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\App\Models\BaseModel;
use Modules\Workspace\Policies\WorkspacePolicy;
use Modules\RolePermission\Models\Permission;

#[UsePolicy(WorkspacePolicy::class)]
class Workspace extends BaseModel
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
            'workspace_permissions',
            'workspace_id',
            'permission_id'
        );
    }

    public function roles()
    {
        return $this->hasMany(\Modules\RolePermission\Models\Role::class, 'workspace_id', 'id');
    }

    public function syncPermissionsAndUpdateModeratorRole(array $permissions): void
    {
        $changes = $this->permissions()->sync($permissions);
        $detached = $changes['detached'] ?? [];

        if (!empty($detached)) {
            $this->roles()->each(function ($role) use ($detached) {
                $role->permissions()->detach($detached);
            });
        }

        $roleName = 'moderator_' . $this->id;
        $role = \Modules\RolePermission\Models\Role::firstOrCreate(
            [
                'name' => $roleName,
                'guard_name' => 'web',
            ],
            [
                'workspace_id' => $this->id,
                'translations' => [
                    'uz' => 'Moderator',
                    'oz' => 'Moderator',
                    'ru' => 'Модератор',
                    'en' => 'Moderator',
                ],
            ]
        );

        if ($role->workspace_id !== $this->id) {
            $role->update(['workspace_id' => $this->id]);
        }

        $role->syncPermissions($permissions);
    }
}

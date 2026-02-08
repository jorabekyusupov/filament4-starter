<?php

namespace Modules\RolePermission\Seeders;

use Illuminate\Database\Seeder;
use Modules\Workspace\Models\Workspace;
use Modules\RolePermission\Models\Permission;
use Modules\RolePermission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::query()
            ->updateOrCreate([
                'name' => 'super_admin',
            ], [
                'guard_name' => 'web',
                'translations' => [
                    'ru' => 'Супер Админ',
                    'en' => 'Super Admin',
                    'uz' => 'Супер Админ',
                    'oz' => 'Super Admin',
                ],
                'is_dont_delete' => true,
                'workspace_id' => Workspace::query()->defaultId(),
            ]);

        $permissions = Permission::query()->pluck('name')->toArray();
        $role->syncPermissions($permissions);
    }
}

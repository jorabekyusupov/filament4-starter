<?php

namespace Modules\User\Seeders;

use Illuminate\Database\Seeder;
use Modules\Organization\Models\Organization;
use Modules\RolePermission\Models\Permission;
use Modules\RolePermission\Models\Role;
use Modules\User\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {


        $orgDefault = Organization::query()->defaultId();
        $user = User::query()->create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'superadmin@info.com',
            'password' => bcrypt('jora'),
            'email_verified_at' => now(),
            'organization_id' => $orgDefault,
            'type' => 'superadmin',
            'status' => true,
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'middle_name' => 'infocomivich',
            'pin' => 10000000000000,
            'dont_touch' => true,
        ]);

        $role = Role::query()
            ->firstOrCreate([
                'name' => 'super_admin',
            ]);

        $user->assignRole($role->name);
    }
}

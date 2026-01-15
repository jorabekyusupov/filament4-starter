<?php

namespace Modules\User\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Organization\Models\Organization;
use Modules\User\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
//        \DB::unprepared(file_get_contents(module_path("core/user/data/users.sql")));
//        $user = User::query()->where('type', 'superadmin')->first();
//        $user1 = User::query()->where('username', 'jorayevolim')->first();
//        $sashaUser = User::query()->where('username', 'aleksander')-> first();
//        $mouser = User::query()->where('username', 'mouser')-> first();

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



       $role = \Spatie\Permission\Models\Role::query()
           ->firstOrCreate([
               'name' => 'super_admin',
               'guard_name' => 'web'
           ]);
           
           $permissions = \Spatie\Permission\Models\Permission::query()->pluck('name')->toArray();
           $role->syncPermissions($permissions);
           $user->assignRole($role->name);
//        \Spatie\Permission\Models\Permission::query()->truncate();
//        \Spatie\Permission\Models\Permission::query()
//            ->insert([
//                [
//                    'name' => 'ViewAny:Role',
//                    'guard_name' => 'web',
//                    'group' => 'role',
//                    'module' => 'system',
//                    'created_at' => now(),
//                    'updated_at' => now(),
//                ],
//                [
//                    'name' => 'View:Role',
//                    'guard_name' => 'web',
//                    'group' => 'role',
//                    'module' => 'system',
//                    'created_at' => now(),
//                    'updated_at' => now(),
//                ],
//                [
//                    'name' => 'Create:Role',
//                    'guard_name' => 'web',
//                    'group' => 'role',
//                    'module' => 'system',
//                    'created_at' => now(),
//                    'updated_at' => now(),
//                ],
//                [
//                    'name' => 'Update:Role',
//                    'guard_name' => 'web',
//                    'group' => 'role',
//                    'module' => 'system',
//                    'created_at' => now(),
//                    'updated_at' => now(),
//                ],
//                [
//                    'name' => 'Delete:Role',
//                    'guard_name' => 'web',
//                    'group' => 'role',
//                    'module' => 'system',
//                    'created_at' => now(),
//                    'updated_at' => now(),
//                ],
//                [
//                    'name' => 'Restore:Role',
//                    'guard_name' => 'web',
//                    'group' => 'role',
//                    'module' => 'system',
//                    'created_at' => now(),
//                    'updated_at' => now()
//                ],
//                [
//                    'name' => 'RestoreAny:Role',
//                    'guard_name' => 'web',
//                    'group' => 'role',
//                    'module' => 'system',
//                    'created_at' => now(),
//                    'updated_at' => now()
//                ],
//                [
//                    'name' => 'ForceDelete:Role',
//                    'guard_name' => 'web',
//                    'group' => 'role',
//                    'module' => 'system',
//                    'created_at' => now(),
//                    'updated_at' => now()
//                ],
//                [
//                    'name' => 'ForceDeleteAny:Role',
//                    'guard_name' => 'web',
//                    'group' => 'role',
//                    'module' => 'system',
//                    'created_at' => now(),
//                    'updated_at' => now()
//                ]
//
//            ]);
//
//
//        $user->assignRole($role->name);
//        $permissions = \Spatie\Permission\Models\Permission::query()->pluck('name')->toArray();
//        $role->syncPermissions($permissions);
//        $data = json_decode(file_get_contents(module_path("core/user/data/users.json")), true);
//        $userInserter = [];
//        $passwords = [];
//        foreach ($data as $item) {
//            $password = Str::random('12');
//            $passwords[$item['number']] = $password;
//            $userInserter[] = [
//                'name' => $item['name'],
//                'password' => bcrypt($password),
//                'email_verified_at' => now(),
//                'pin' => $item['number'],
//                'status' => true,
//            ];
//        }
//        $password = json_encode($passwords, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
//        file_put_contents(module_path("core/user/data/user_passwords.json"), $password);
//        User::query()->insert($userInserter);
    }
}

<?php

namespace Database\Seeders;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Language\Seeders\DefaultLangSeeder;
use Modules\MakerModule\Seeders\MakerModuleSeeder;
use Modules\Workspace\Seeders\WorkspaceSeeder;
use Modules\RolePermission\Seeders\RolePermissionSeeder;
use Modules\RolePermission\Seeders\ShieldSeeder;
use Modules\User\Seeders\UserSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DefaultLangSeeder::class,
            MakerModuleSeeder::class,
            WorkspaceSeeder::class,
            ShieldSeeder::class,
            RolePermissionSeeder::class,
            UserSeeder::class,
        ]);

    }
}

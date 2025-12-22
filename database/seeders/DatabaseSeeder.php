<?php

namespace Database\Seeders;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Language\Seeders\DefaultLangSeeder;
use Modules\MakerModule\Seeders\MakerModuleSeeder;
use Modules\Organization\Seeders\OrganizationSeeder;
use Modules\User\Seeders\UserSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            DefaultLangSeeder::class,
            MakerModuleSeeder::class,
            OrganizationSeeder::class,
            ShieldSeeder::class,
            UserSeeder::class,
        ]);

    }
}

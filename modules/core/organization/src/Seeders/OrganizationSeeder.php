<?php

namespace Modules\Organization\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Organization\Models\Organization;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        Organization::query()
            ->create([
                'name' => [
                    "en" => "System Management Organization",
                    "oz" => "Tizim boshqaruv tashkiloti",
                    "ru" => "Организация управления системой",
                    "uz" => "Тизим бошқарув ташкилоти"
                ],
                'slug' => 'default',
                'structure_id' => null,
                'hidden' => true,
                'is_dont_delete' => true,
                'status' => true,
            ]);


    }
}

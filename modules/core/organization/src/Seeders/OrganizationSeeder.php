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
                    'ru' => 'DEFAULT',
                    'oz' => 'DEFAULT',
                    'uz' => 'DEFAULT',
                    'en' => 'DEFAULT',
                ],
                'slug' => 'default',
                'structure_id' => null,
                'hidden' => true,
            ]);


    }
}

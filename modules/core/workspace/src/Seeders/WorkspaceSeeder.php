<?php

namespace Modules\Workspace\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Workspace\Models\Workspace;

class WorkspaceSeeder extends Seeder
{
    public function run(): void
    {
        Workspace::query()
            ->create([
                'name' => [
                    "en" => "System Management Workspace",
                    "oz" => "Tizim boshqaruv workspace",
                    "ru" => "Рабочее пространство управления системой",
                    "uz" => "Тизим бошқарув workspace"
                ],
                'slug' => 'default',
                'structure_id' => null,
                'hidden' => true,
                'is_dont_delete' => true,
                'status' => true,
            ]);


    }
}

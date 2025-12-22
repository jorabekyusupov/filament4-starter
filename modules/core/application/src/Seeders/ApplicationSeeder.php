<?php

namespace Modules\Application\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Application\Models\Application;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        Application::query()->truncate();



    }
}

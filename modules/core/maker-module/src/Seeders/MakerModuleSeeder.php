<?php

namespace Modules\MakerModule\Seeders;

use Illuminate\Database\Seeder;
use Modules\MakerModule\Models\Module;

class MakerModuleSeeder extends Seeder
{
    /**
     * @throws \JsonException
     */
    public function run(): void
    {
        \Illuminate\Support\Facades\Artisan::call('sync:module');
    }


}

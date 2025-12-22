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

        Module::query()->truncate();
        $data = module_path('core/maker-module/data/data.json');
        $json = json_decode(file_get_contents($data), true, 512, JSON_THROW_ON_ERROR);
        foreach ($json as $key => $item) {
            $json[$key]['created_at'] = now();
            $json[$key]['updated_at'] = now();
        }
        Module::query()->insert($json);
    }


}

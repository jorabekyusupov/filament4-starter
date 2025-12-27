<?php

namespace Modules\MakerModule\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Modules\MakerModule\Models\Module;
use function App\Console\Commands\now;

class SyncModuleCommand extends Command
{
    protected $signature = 'sync:module';

    protected $description = 'Command description';

    public function handle(): void
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

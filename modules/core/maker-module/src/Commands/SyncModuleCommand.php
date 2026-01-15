<?php

namespace Modules\MakerModule\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Modules\MakerModule\Models\Module;


class SyncModuleCommand extends Command
{
    protected $signature = 'sync:module';

    protected $description = 'Command description';

    public function handle(): void
    {
        // Truncate tables to ensure clean sync
        \Modules\MakerModule\Models\ModuleTableColumn::query()->truncate();
        \Modules\MakerModule\Models\ModuleTable::query()->truncate();
        Module::query()->truncate();

        $data = module_path('core/maker-module/data/data.json');
        
        if (!file_exists($data)) {
            $this->error('Data file not found: ' . $data);
            return;
        }

        $json = json_decode(file_get_contents($data), true, 512, JSON_THROW_ON_ERROR);
        
        foreach ($json as $moduleData) {
            $module = Module::create([
                'name' => $moduleData['name'],
                'description' => $moduleData['description'] ?? ($moduleData['name'] . " module"),
                'alias' => Str::studly($moduleData['name']), // Or namespace?
                'path' => $moduleData['path'],
                'status' => $moduleData['status'] ?? true,
                'namespace' => $moduleData['namespace'] ?? 'Modules\\' . Str::studly($moduleData['name']),
                'user_id' => 1, // Default user
                'created_at' => $moduleData['created_at'] ?? \Illuminate\Support\Facades\Date::now(),
                'updated_at' => $moduleData['updated_at'] ?? \Illuminate\Support\Facades\Date::now(),
            ]);
            
            if (isset($moduleData['tables']) && is_array($moduleData['tables'])) {
                foreach ($moduleData['tables'] as $tableData) {
                    $table = $module->tables()->create([
                        'name' => $tableData['name'],
                        'soft_deletes' => $tableData['soft_deletes'] ?? false,
                        'logged' => $tableData['logged'] ?? false,
                        'status' => $tableData['status'] ?? true,
                        'user_id' => 1,
                    ]);
                    
                    if (isset($tableData['columns']) && is_array($tableData['columns'])) {
                        foreach ($tableData['columns'] as $columnData) {
                            $options = $columnData['options'] ?? [];
                            $table->columns()->create([
                                'name' => $columnData['name'],
                                'type' => $columnData['type'],
                                // Map explicit attributes from options
                                'nullable' => $options['nullable'] ?? false,
                                'unique' => $options['unique'] ?? false,
                                'index' => $options['index'] ?? false,
                                'foreign' => isset($options['related_model']),
                                'foreign_table' => $options['related_model'] ?? null,
                                'foreign_column' => $options['related_column'] ?? null,
                                
                                'options' => is_array($options) ? json_encode($options) : $options,
                                'module_id' => $module->id,
                                'status' => true,
                                'user_id' => 1,
                            ]);
                        }
                    }
                }
            }
        }
        
        $this->info('Modules synced successfully.');
    }
}

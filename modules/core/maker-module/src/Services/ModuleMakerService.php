<?php

namespace Modules\MakerModule\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Jora\Modular\Console\Commands\Make\MakeModule;
use Modules\MakerModule\Models\Module;

class ModuleMakerService
{

    public function create(array $data): Module
    {
        $moduleNameArr = explode('/', $data['name']);
        
        if (count($moduleNameArr) > 1) {
            [$moduleGroup, $pureName] = $moduleNameArr;
        } else {
            $moduleGroup = $data['group'];
            $pureName = $data['name'];
        }

        $moduleSlug = Str::slug($pureName);
        $studlyModuleName = Str::studly($pureName);
        
        $commandName = $moduleGroup ? $moduleGroup . '/' . $moduleSlug : $moduleSlug;
        
        $tables = $data['tables'] ?? [];
        

        $moduleModel = $this->createModule($pureName, $studlyModuleName, $commandName);

        if (!empty($tables)) {
            foreach ($tables as $table) {
                $tableName = $table['name'];
                $columns = $table['columns'];
                $tableModel = $moduleModel->tables()->create([
                    'name' => $tableName,
                    'soft_deletes' => $table['soft_deletes'] ?? false,
                    'logged' => $table['logged'] ?? false,
                    'status' => $table['status'] ?? true,
                    'user_id' => auth()->id(),
                ]);
                
                $softDeletes = $table['soft_deletes'] ?? false;
                $logged = $table['logged'] ?? false;
                $status = $table['status'] ?? true;
                
                $columns = array_merge(
                    $columns,
                    $softDeletes ? [['name' => 'deleted_at', 'type' => 'softDeletes']] : [],
                    $logged ? [['name' => 'created_by', 'type' => 'unsignedBigInteger'], ['name' => 'updated_by', 'type' => 'unsignedBigInteger']] : [],
                    $status ? [['name' => 'status', 'type' => 'boolean']] : []
                );

                $columnsForMigration = array_combine(array_column($columns, 'name'), array_column($columns, 'type'));
                $this->createMigrationFile($commandName, $tableName, $columnsForMigration);
                $this->createModelFile($commandName, $data['name'], $tableName, $columnsForMigration, $softDeletes);

                // Add Filament Resource creation if has_resource is true
                if ($table['has_resource'] ?? false) {
                    $resourceData = [
                        'name' => Str::studly(Str::singular($tableName)),
                        'model_name' => Str::studly(Str::singular($tableName)),
                    ];
                    
                    // Find layout for this table
                    $tableLayout = collect($data['resource_layouts'] ?? [])->firstWhere('table_name', $tableName);
                    $layoutSchema = $tableLayout['schema'] ?? [];

                    $this->createFilamentResource($commandName, $data['name'], $resourceData, $columnsForMigration, $layoutSchema);
                }

                foreach ($columnsForMigration as $columnName => $columnType) {
                    $tableModel
                        ->columns()
                        ->create([
                            'name' => $columnName,
                            'type' => $columnType,
                            'module_id' => $moduleModel->id,
                            'status' => true,
                            'user_id' => auth()->id(),
                        ]);
                }

            }
        }
        
        return $moduleModel;
    }

    public function createModule($name, $studlyModuleName, $commandName): Module
    {
        Artisan::call(MakeModule::class, [
            'name' => $commandName,
            '--accept-default-namespace' => true,
        ]);
        $dataJson = module_path('core/maker-module/data/data.json');
        
        // Ensure directory exists if creating for the first time
        if (!file_exists($dataJson)) {
            $dir = dirname($dataJson);
            if (!is_dir($dir)) {
                 mkdir($dir, 0755, true);
            }
            file_put_contents($dataJson, '[]');
        }

        $currentData = json_decode(file_get_contents($dataJson), true, 512, JSON_THROW_ON_ERROR);
        $currentData[] = [
            'name' => $name,
            'source' => 'admin',
            'namespace' => 'Modules\\' . $studlyModuleName,
            'path' => 'modules/' . $commandName,
            'status' => true,
            'stable' => false,
        ];
        file_put_contents($dataJson, json_encode($currentData, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
        
        return Module::create([
            'name' => $name,
            'namespace' => 'Modules\\' . $studlyModuleName,
            'path' => 'modules/' . $commandName,
            'user_id' => auth()->id(),
        ]);
    }

    public function createFilamentResource($commandName, $moduleName, $resourceData, $columns, $layoutData)
    {
        $resourceName = Str::studly($resourceData['name']);
        $modelName = Str::studly($resourceData['model_name'] ?? Str::singular($resourceData['name']));

        // Create the main Resource file
        $this->createResourceFile($commandName, $moduleName, $resourceName, $modelName, $columns, $layoutData);

        // Create Resource Pages
        $this->createResourcePages($commandName, $moduleName, $resourceName, $modelName);
    }

    public function createResourceFile($commandName, $moduleName, $resourceName, $modelName, $columns, $layoutData)
    {
        $resourceFile = base_path("modules/{$commandName}/src/Filament/Resources/{$resourceName}Resource.php");
        $stub = file_get_contents(base_path('packages/modular/stubs/Filament/Resources/StubTableNameResource.php'));

        // Ensure Resources directory exists
        $resourcesDir = dirname($resourceFile);
        if (!is_dir($resourcesDir)) {
            mkdir($resourcesDir, 0755, true);
        }

        $resourceFileContent = str_replace(
            [
                'StubModuleNamespace',
                'StubSubModulePrefix',
                'StubTableName',
                'StubTableNames'
            ],
            [
                'Modules',
                $resourceName,
                $modelName,
                Str::plural($resourceName)
            ],
            $stub
        );

        $formSchema = $this->generateFormSchema($columns, $layoutData);
        $resourceFileContent = str_replace('// Add your form fields here', $formSchema, $resourceFileContent);

        file_put_contents($resourceFile, $resourceFileContent);
    }

    public function createResourcePages($commandName, $moduleName, $resourceName, $modelName)
    {
        $pages = [
            'List' => 'ListStubTableNames.php',
            'Create' => 'CreateStubTableName.php',
            'Edit' => 'EditStubTableName.php',
            'View' => 'ViewStubTableName.php'
        ];

        foreach ($pages as $pageType => $stubFile) {
            $pageName = $pageType . $resourceName;
            $pageFile = base_path("modules/{$commandName}/src/Filament/Resources/{$resourceName}Resource/Pages/{$pageName}.php");
            $stub = file_get_contents(base_path("packages/modular/stubs/Filament/Resources/Pages/{$stubFile}"));

            // Ensure Pages directory exists
            $pagesDir = dirname($pageFile);
            if (!is_dir($pagesDir)) {
                mkdir($pagesDir, 0755, true);
            }

            $pageContent = str_replace(
                [
                    'StubModuleNamespace',
                    'StubSubModulePrefix',
                    'StubTableName',
                    'StubTableNames'
                ],
                [
                    'Modules',
                    $resourceName,
                    $modelName,
                    Str::plural($resourceName)
                ],
                $stub
            );

            file_put_contents($pageFile, $pageContent);
        }
    }

    public function castsConvertByColumnTypes($columns)
    {
        $types = collect($this->getDataTypes(null))->keys()->toArray();
        // filter not use if on short
        $columns = array_filter($columns, static function ($type) use ($types) {
            return in_array($type, $types, true);
        });
        // json and jsonb to array
        $columns = array_map(static function ($type) {
            return in_array($type, ['json', 'jsonb'], true) ? 'array' : $type;
        }, $columns);
        // convert to array
        return array_combine(array_keys($columns), array_values($columns));
    }

    public function createMigrationFile($commandName, $tableName, $columns)
    {
        $migrationName = 'create_' . Str::plural($tableName) . '_table';
        $migrationFileName = now()->format('Y_m_d_His') . '_' . $migrationName . '.php';
        $migrationFile = base_path("modules/{$commandName}/migrations/{$migrationFileName}");
        $stub = file_get_contents(base_path('packages/modular/stubs/migration.php'));
        
        // Ensure dir exists
        $migDir = dirname($migrationFile);
        if(!is_dir($migDir)) {
            mkdir($migDir, 0755, true);
        }

        $columnsContent = collect($columns)
            ->map(fn($type, $column) => "            \$table->{$type}('{$column}');")
            ->implode("\n");
        $migrationFileContent = str_replace(array('StubModuleName', '//columns'), array(Str::plural($tableName), $columnsContent), $stub);
        $migrationFileContent = preg_replace('/\n\s*\n/', "\n", $migrationFileContent);
        file_put_contents($migrationFile, $migrationFileContent);
    }

    public function createModelFile($commandName, $moduleName, $tableName, $columns, $softDeletes = false)
    {
        $modelName = Str::studly(Str::singular($tableName));
        $policyClass = $modelName . 'Policy';

        $modelFile = base_path("modules/{$commandName}/src/Models/{$modelName}.php");
        $stub = file_get_contents(base_path('packages/modular/stubs/model.php'));

        // Ensure Models directory exists
        $modelsDir = dirname($modelFile);
        if (!is_dir($modelsDir)) {
            mkdir($modelsDir, 0755, true);
        }

        // Generate the SoftDeletes trait usage (without quotes)
        $softDeletesTrait = $softDeletes ? "use SoftDeletes;\n" : '';

        $modelFileContent = str_replace(
            ['StubModuleNamespace', 'StubSubModulePrefix', 'StubTableName', 'fillables', 'table_name', 'StubPolicyClass', 'useSoftDeletes'],
            ['Modules', $modelName, $modelName, implode("',\n        '", array_keys($columns)), Str::plural($tableName), $policyClass, $softDeletesTrait],
            $stub
        );

        // Add the SoftDeletes import if needed
        if ($softDeletes) {
            $modelFileContent = str_replace(
                'use Illuminate\Database\Eloquent\Model;',
                "use Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\SoftDeletes;",
                $modelFileContent
            );
        }

        file_put_contents($modelFile, $modelFileContent);

        $castsContent = collect($this->castsConvertByColumnTypes($columns))
            ->map(fn($type, $column) => "        '{$column}' => '{$type}',")
            ->implode("\n");
        $castsContent = trim($castsContent, ",\n");

        $modelFileContent = str_replace('castsContent', $castsContent, $modelFileContent);
        file_put_contents($modelFile, $modelFileContent);

        // Create Policy file
        $this->createPolicyFile($commandName, $moduleName, $tableName, $softDeletes);
    }

    public function createPolicyFile($commandName, $moduleName, $tableName, $softDeletes = false)
    {
        $modelName = Str::studly(Str::singular($tableName));
        $policyName = $modelName . 'Policy';
        $policyFile = base_path("modules/{$commandName}/src/Policies/{$policyName}.php");
        $stub = file_get_contents(base_path('packages/modular/stubs/Policy.php'));

        // Ensure Policies directory exists
        $policiesDir = dirname($policyFile);
        if (!is_dir($policiesDir)) {
            mkdir($policiesDir, 0755, true);
        }

        // Generate permission name (kebab-case)
        $permissionName = Str::kebab($tableName);

        $policyFileContent = str_replace(
            [
                'StubModuleNamespace',
                'StubSubModulePrefix',
                'StubTableName',
                'stubTableName'
            ],
            [
                'Modules',
                $modelName,
                $modelName,
                $permissionName
            ],
            $stub
        );

        file_put_contents($policyFile, $policyFileContent);
    }

    public function getDataTypes($search = null)
    {
        return collect([
            'string' => 'string',
            'char' => 'char',
            'text' => 'text',
            'mediumText' => 'mediumText',
            'longText' => 'longText',
            'integer' => 'integer',
            'tinyInteger' => 'tinyInteger',
            'smallInteger' => 'smallInteger',
            'mediumInteger' => 'mediumInteger',
            'bigInteger' => 'bigInteger',
            'unsignedInteger' => 'unsignedInteger',
            'unsignedBigInteger' => 'unsignedBigInteger',
            'float' => 'float',
            'double' => 'double',
            'decimal' => 'decimal',
            'boolean' => 'boolean',
            'date' => 'date',
            'datetime' => 'datetime',
            'timestamp' => 'timestamp',
            'time' => 'time',
            'year' => 'year',
            'binary' => 'binary',
            'json' => 'json',
            'jsonb' => 'jsonb',
            'uuid' => 'uuid',
            'ipAddress' => 'ipAddress',
            'macAddress' => 'macAddress',
            'geometry' => 'geometry',
            'point' => 'point',
            'lineString' => 'lineString',
            'polygon' => 'polygon',
            'multiPoint' => 'multiPoint',
            'multiLineString' => 'multiLineString',
            'multiPolygon' => 'multiPolygon',
            'geometryCollection' => 'geometryCollection',
            'enum' => 'enum',
            'set' => 'set',
            'morphs' => 'morphs',
            'nullableMorphs' => 'nullableMorphs',
            'rememberToken' => 'rememberToken',
        ])
            ->map(function ($value, $key) {
                return [$key => __($value)];
            })
            ->filter(fn($value, $key) => !$search || Str::contains($key, $search))
            ->collapse()
            ->toArray();
    }


    private function generateFormSchema(array $columns, array $builderBlocks): string
    {
        // If no builder blocks, fallback to simple list of all columns
        if (empty($builderBlocks)) {
            $fields = [];
            foreach ($columns as $name => $type) {
                $fields[] = $this->getFieldString($name, $type);
            }
            return implode("\n                ", $fields);
        }

        $components = [];

        foreach ($builderBlocks as $block) {
            $type = $block['type'];
            $data = $block['data'] ?? [];

            switch ($type) {
                case 'field':
                    $colName = $data['column'] ?? null;
                    $isTranslatable = $data['is_translatable'] ?? false;
                    
                    if ($isTranslatable && $colName) {
                        $components[] = "...getNameInputsFilament('{$colName}')";
                    } elseif ($colName && isset($columns[$colName])) {
                        $components[] = $this->getFieldString($colName, $columns[$colName]);
                    } elseif ($colName) {
                         // Fallback if type not found (shouldn't happen if validated)
                        $components[] = $this->getFieldString($colName, 'string');
                    }
                    break;

                case 'section':
                    $label = $data['label'] ?? 'Section';
                    $innerBlocks = $data['schema'] ?? []; // Builder inside section
                    $innerContent = $this->generateFormSchema($columns, $innerBlocks);
                    $components[] = "\Filament\Schemas\Components\Section::make('{$label}')\n                    ->schema([\n                        " . str_replace("\n", "\n    ", $innerContent) . "\n                    ])";
                    break;

                case 'tabs':
                    $tabs = $data['tabs'] ?? [];
                    $tabComponents = [];
                    foreach ($tabs as $tab) {
                        $tabLabel = $tab['label'] ?? 'Tab';
                        $tabBlocks = $tab['schema'] ?? [];
                        $tabContent = $this->generateFormSchema($columns, $tabBlocks);
                        $tabComponents[] = "\Filament\Schemas\Components\Tabs\Tab::make('{$tabLabel}')\n                            ->schema([\n                                " . str_replace("\n", "\n        ", $tabContent) . "\n                            ])";
                    }
                    $tabsContentString = implode(",\n                        ", $tabComponents);
                    $components[] = "\Filament\Schemas\Components\Tabs::make('Tabs')\n                    ->tabs([\n                        {$tabsContentString}\n                    ])";
                    break;
                
                case 'fieldset':
                    $label = $data['label'] ?? 'Fieldset';
                    $innerBlocks = $data['schema'] ?? [];
                    $innerContent = $this->generateFormSchema($columns, $innerBlocks);
                    $components[] = "\Filament\Schemas\Components\Fieldset::make('{$label}')\n                    ->schema([\n                        " . str_replace("\n", "\n    ", $innerContent) . "\n                    ])";
                    break;
                
                case 'grid':
                    $gridCols = $data['columns'] ?? 2;
                    $gridItems = $data['items'] ?? []; 
                    // items is expected to be { 0: [...], 1: [...] } for columns
                    
                    $colComponents = [];
                    for ($i = 0; $i < $gridCols; $i++) {
                        $colBlocks = $gridItems[$i] ?? [];
                        if (empty($colBlocks)) {
                             // Empty column placeholder to maintain grid structure? 
                             // Or just empty group.
                             $colComponents[] = "\Filament\Schemas\Components\Group::make()\n                        ->schema([])\n                        ->columnSpan(1)";
                             continue;
                        }
                        $colContent = $this->generateFormSchema($columns, $colBlocks);
                        $colComponents[] = "\Filament\Schemas\Components\Group::make()\n                        ->schema([\n                            " . str_replace("\n", "\n    ", $colContent) . "\n                        ])\n                        ->columnSpan(1)";
                    }
                    
                    $gridContent = implode(",\n                        ", $colComponents);
                    
                    $components[] = "\Filament\Schemas\Components\Grid::make({$gridCols})\n                    ->schema([\n                        {$gridContent}\n                    ])";
                    break;
            }
        }

        return implode(",\n                ", $components);
    }

    private function getFieldString($name, $type): string
    {
        $label = Str::headline($name);
        
        // Basic type mapping
        switch ($type) {
            case 'boolean':
                return "\Filament\Forms\Components\Toggle::make('{$name}')->label('{$label}')";
            case 'text':
            case 'mediumText':
            case 'longText':
            case 'json':
            case 'jsonb':
                return "\Filament\Forms\Components\Textarea::make('{$name}')->label('{$label}')";
            case 'date':
                return "\Filament\Forms\Components\DatePicker::make('{$name}')->label('{$label}')";
            case 'datetime':
            case 'timestamp':
                return "\Filament\Forms\Components\DateTimePicker::make('{$name}')->label('{$label}')";
            case 'integer':
            case 'tinyInteger':
            case 'smallInteger':
            case 'mediumInteger':
            case 'bigInteger':
            case 'unsignedInteger':
            case 'unsignedBigInteger':
            case 'decimal':
            case 'float':
            case 'double':
                return "\Filament\Forms\Components\TextInput::make('{$name}')->label('{$label}')->numeric()";
            default:
                return "\Filament\Forms\Components\TextInput::make('{$name}')->label('{$label}')";
        }
    }
}

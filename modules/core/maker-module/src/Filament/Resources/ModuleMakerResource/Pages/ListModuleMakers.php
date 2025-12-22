<?php

namespace Modules\MakerModule\Filament\Resources\ModuleMakerResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Jora\Modular\Console\Commands\Make\MakeModule;
use Modules\MakerModule\Filament\Resources\ModuleMakerResource;
use Modules\MakerModule\Models\Module;

class ListModuleMakers extends ListRecords
{
    protected static string $resource = ModuleMakerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->modal()
                ->modalWidth(Width::SixExtraLarge)
                ->action(function ($data) {
//                    dd($data);
                    $moduleSlug = Str::slug($data['name']);
                    $studlyModuleName = Str::studly($data['name']);
                    $moduleNameArr = explode('/', $data['name']);
                    $tables = $data['tables'] ?? [];
                    if (count($moduleNameArr) > 1) {
                        [$moduleGroup, $name] = $moduleNameArr;
                    } else {
                        $moduleGroup = $data['group'];
                    }
                    $commandName = $moduleGroup ? $moduleGroup . '/' . $moduleSlug : $moduleSlug;
                    $moduleModel = $this->createModule($data['name'], $studlyModuleName, $commandName);

                    if (!empty($data['tables'])) {
                        foreach ($data['tables'] as $table) {
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
                            $timestamps = $table['timestamps'] ?? false;
                            $logged = $table['logged'] ?? false;
                            $status = $table['status'] ?? true;
                            $columns = array_merge(
                                $columns,
                                $softDeletes ? [['name' => 'deleted_at', 'type' => 'softDeletes']] : [],
                                $logged ? [['name' => 'created_by', 'type' => 'unsignedBigInteger'], ['name' => 'updated_by', 'type' => 'unsignedBigInteger']] : [],
                                $status ? [['name' => 'status', 'type' => 'boolean']] : []
                            );

                            $columns = array_combine(array_column($columns, 'name'), array_column($columns, 'type'));
                            $this->createMigrationFile($commandName, $tableName, $columns);
                            $this->createModelFile($commandName, $data['name'], $tableName, $columns, $softDeletes);

                            // Add Filament Resource creation if has_resource is true
                            if ($table['has_resource'] ?? false) {
                                $resourceData = [
                                    'name' => Str::studly(Str::singular($tableName)),
                                    'model_name' => Str::studly(Str::singular($tableName)),
                                ];
                                $this->createFilamentResource($commandName, $data['name'], $resourceData);
                            }

                            foreach ($columns as $columnName => $columnType) {
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
                })
        ];
    }

    public function createModule($name, $studlyModuleName, $commandName): Module
    {

        Artisan::call(MakeModule::class, [
            'name' => $commandName,
            '--accept-default-namespace' => true,
        ]);
        $dataJson = module_path('core/maker-module/data/data.json');
        $data = json_decode(file_get_contents($dataJson), true, 512, JSON_THROW_ON_ERROR);
        $data[] = [
            'name' => $name,
            'source' => 'admin',
            'namespace' => 'Modules\\' . $studlyModuleName,
            'path' => 'modules/' . $commandName,
            'status' => true,
            'stable' => false,
        ];
        file_put_contents($dataJson, json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
        return Module::create([
            'name' => $name,
            'namespace' => 'Modules\\' . $studlyModuleName,
            'path' => 'modules/' . $commandName,
            'user_id' => auth()->id(),
        ]);


    }

    public function createFilamentResource($commandName, $moduleName, $resourceData)
    {
        $resourceName = Str::studly($resourceData['name']);
        $modelName = Str::studly($resourceData['model_name'] ?? Str::singular($resourceData['name']));

        // Create the main Resource file
        $this->createResourceFile($commandName, $moduleName, $resourceName, $modelName);

        // Create Resource Pages
        $this->createResourcePages($commandName, $moduleName, $resourceName, $modelName);
    }

    public function createResourceFile($commandName, $moduleName, $resourceName, $modelName)
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


    public
    function castsConvertByColumnTypes($columns)
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

    public
    function createMigrationFile($commandName, $tableName, $columns)
    {
        $migrationName = 'create_' . Str::plural($tableName) . '_table';
        $migrationFileName = now()->format('Y_m_d_His') . '_' . $migrationName . '.php';
        $migrationFile = base_path("modules/{$commandName}/migrations/{$migrationFileName}");
        $stub = file_get_contents(base_path('packages/modular/stubs/migration.php'));

        $columnsContent = collect($columns)
            ->map(fn($type, $column) => "            \$table->{$type}('{$column}');")
            ->implode("\n");
        $migrationFileContent = str_replace(array('StubModuleName', '//columns'), array(Str::plural($tableName), $columnsContent), $stub);
        $migrationFileContent = preg_replace('/\n\s*\n/', "\n", $migrationFileContent);
        file_put_contents($migrationFile, $migrationFileContent);
    }

    public
    function createModelFile($commandName, $moduleName, $tableName, $columns, $softDeletes = false)
    {
        $modelName = Str::studly(Str::singular($tableName));
        $policyClass = $modelName . 'Policy';

        $modelFile = base_path("modules/{$commandName}/src/Models/{$modelName}.php");
        $stub = file_get_contents(base_path('packages/modular/stubs/model.php'));

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

    public
    function getDataTypes($search = null)
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
}

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
        // Extract layouts arrays
        $resourceLayouts = $data['resource_layouts'] ?? [];
        $tableLayouts = $data['table_layouts'] ?? [];
        

        $moduleModel = $this->createModule($pureName, $studlyModuleName, $commandName, $tables);

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
                    $logged ? array_merge(
                        [['name' => 'created_by', 'type' => 'foreignId', 'related_model' => 'App\Models\User', 'nullable' => true], 
                         ['name' => 'updated_by', 'type' => 'foreignId', 'related_model' => 'App\Models\User', 'nullable' => true]],
                        $softDeletes ? [['name' => 'deleted_by', 'type' => 'foreignId', 'related_model' => 'App\Models\User', 'nullable' => true]] : []
                    ) : [],
                    $status ? [['name' => 'status', 'type' => 'boolean']] : []
                );

                // Prepare columns with full metadata
                $columnsAssociative = [];
                foreach ($columns as $col) {
                    $columnsAssociative[$col['name']] = $col;
                }
                
                $columnsForMigration = $columnsAssociative; // Pass full data to support modifiers

                $this->createMigrationFile($commandName, $tableName, $columnsForMigration); 
                $this->createModelFile($commandName, $data['name'], $tableName, $columnsAssociative, $softDeletes);

                // Add Filament Resource creation if has_resource is true
                if ($table['has_resource'] ?? false) {
                    $resourceData = [
                        'name' => Str::studly(Str::singular($tableName)),
                        'model_name' => Str::studly(Str::singular($tableName)),
                    ];
                    
                    // Find layout for this table
                    $tableLayout = collect($resourceLayouts)->firstWhere('table_name', $tableName);
                    $formSchema = $tableLayout['schema'] ?? [];

                    $tableBuilder = collect($tableLayouts)->firstWhere('table_name', $tableName);
                    $tableSchema = $tableBuilder['schema'] ?? [];

                    $this->createFilamentResource($commandName, $data['name'], $resourceData, $columnsAssociative, $formSchema, $tableSchema);
                }

                foreach ($columns as $col) {
                    $tableModel
                        ->columns()
                        ->create([
                            'name' => $col['name'],
                            'type' => $col['type'],
                            // Map explicit attributes
                            'nullable' => $col['nullable'] ?? false,
                            'unique' => $col['unique'] ?? false,
                            'index' => $col['index'] ?? false,
                            // Map foreign key details if available (assuming related_model logic implies foreign)
                            'foreign' => isset($col['related_model']),
                            'foreign_table' => $col['related_model'] ?? null, // storing model here for reference, or table name if we had it
                            'foreign_column' => $col['related_column'] ?? null,
                            
                            'options' => json_encode($col),
                            'module_id' => $moduleModel->id,
                            'status' => true,
                            'user_id' => auth()->id(),
                        ]);
                }

            }
        }
        
        return $moduleModel;
    }

    public function createModule($name, $studlyModuleName, $commandName, $tables = []): Module
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
        
        // Prepare tables data for persistence
        $tablesData = [];
        foreach ($tables as $table) {
            $columnsData = [];
            foreach ($table['columns'] as $column) {
                $columnsData[] = [
                    'name' => $column['name'],
                    'type' => $column['type'],
                    'options' => $column, // Store full metadata including related_model etc.
                ];
            }
            
            $tablesData[] = [
                'name' => $table['name'],
                'soft_deletes' => $table['soft_deletes'] ?? false,
                'logged' => $table['logged'] ?? false,
                'status' => $table['status'] ?? true,
                'columns' => $columnsData,
            ];
        }

        $newItem = [
            'name' => $name,
            'source' => 'admin',
            'namespace' => 'Modules\\' . $studlyModuleName,
            'path' => 'modules/' . $commandName,
            'status' => true,
            'stable' => false,
            'tables' => $tablesData,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Check if exists and update or append
        $exists = false;
        foreach ($currentData as $key => $item) {
            if ($item['path'] === $newItem['path']) {
                $currentData[$key] = array_merge($item, $newItem);
                $exists = true;
                break;
            }
        }
        
        if (!$exists) {
            $currentData[] = $newItem;
        }

        file_put_contents($dataJson, json_encode($currentData, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
        
        return Module::create([
            'name' => $name,
            'namespace' => 'Modules\\' . $studlyModuleName,
            'path' => 'modules/' . $commandName,
            'user_id' => auth()->id(),
        ]);
    }

    public function createFilamentResource($commandName, $moduleName, $resourceData, $columns, $formLayoutData, $tableLayoutData = [])
    {
        $resourceName = Str::studly($resourceData['name']);
        $modelName = Str::studly($resourceData['model_name'] ?? Str::singular($resourceData['name']));

        // Create the main Resource file
        $this->createResourceFile($commandName, $moduleName, $resourceName, $modelName, $columns, $formLayoutData, $tableLayoutData);

        // Create Schemas/Form file
        $this->createFormFile($commandName, $moduleName, $resourceName, $modelName, $columns, $formLayoutData);

        // Create Tables/Table file
        $this->createTableFile($commandName, $moduleName, $resourceName, $modelName, $columns, $tableLayoutData);

        // Create Resource Pages
        $this->createResourcePages($commandName, $moduleName, $resourceName, $modelName);

        // Create Language Files
        $this->createLanguageFiles($commandName, $resourceData['translations'] ?? []);
    }

    public function createResourceFile($commandName, $moduleName, $resourceName, $modelName, $columns, $formLayoutData, $tableLayoutData = [])
    {
        $resourceFile = base_path("modules/{$commandName}/src/Filament/Resources/{$resourceName}Resource/{$resourceName}Resource.php");
        $stub = file_get_contents(base_path('packages/modular/stubs/Filament/Resources/StubTableNameResource.php'));

        // Ensure Resources directory exists
        $resourcesDir = dirname($resourceFile);
        if (!is_dir($resourcesDir)) {
            mkdir($resourcesDir, 0755, true);
        }

        // Extract configuration for this specific resource
        $layoutConfig = collect($formLayoutData['resource_layouts'] ?? [])
            ->firstWhere('table_name', $resourceData['table_name'] ?? $columns[0]['table_name'] ?? '');

        // Defaults
        $navLabel = $layoutConfig['navigation_label'] ?? Str::title(Str::plural($resourceName));
        $navGroup = $layoutConfig['navigation_group'] ?? 'Content';
        $navIcon = $layoutConfig['navigation_icon'] ?? 'heroicon-o-rectangle-stack';
        $navSort = $layoutConfig['navigation_sort'] ?? 1;
        $modelLabel = $layoutConfig['model_label'] ?? Str::title($modelName);
        $pluralModelLabel = $layoutConfig['plural_model_label'] ?? Str::title(Str::plural($modelName));

        $resourceFileContent = str_replace(
            [
                'StubModuleNamespace',
                'StubSubModulePrefix',
                'StubTableName',
                'StubTableNames',
                'StubForm',
                'StubTable',
                'StubNavigationLabel',
                'StubNavigationGroup',
                'StubNavigationIcon',
                'StubNavigationSort',
                'StubModelLabel',
                'StubPluralModelLabel'
            ],
            [
                'Modules',
                $resourceName,
                $modelName,
                Str::plural($resourceName),
                $modelName . 'Form',
                $modelName . 'Table',
                $navLabel,
                $navGroup,
                $navIcon,
                $navSort,
                $modelLabel,
                $pluralModelLabel
            ],
            $stub
        );
        
        // No need to inject schema here anymore, as it delegates to Form and Table classes.
        
        file_put_contents($resourceFile, $resourceFileContent);
    }

    public function createFormFile($commandName, $moduleName, $resourceName, $modelName, $columns, $layoutData)
    {
        $formFile = base_path("modules/{$commandName}/src/Filament/Resources/{$resourceName}Resource/Schemas/{$modelName}Form.php");
        $stub = file_get_contents(base_path('packages/modular/stubs/Filament/Resources/Schemas/StubForm.php'));

        $dir = dirname($formFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $content = str_replace(
            [
                'StubModuleNamespace',
                'StubSubModulePrefix',
                'StubTableName',
                'StubForm'
            ],
            [
                'Modules',
                $resourceName,
                $modelName,
                $modelName . 'Form'
            ],
            $stub
        );

        $formSchema = $this->generateFormSchema($columns, $layoutData);
        $content = str_replace('// Add your form fields here', $formSchema, $content);

        file_put_contents($formFile, $content);
    }

    public function createTableFile($commandName, $moduleName, $resourceName, $modelName, $columns, $layoutData)
    {
        $tableFile = base_path("modules/{$commandName}/src/Filament/Resources/{$resourceName}Resource/Tables/{$modelName}Table.php");
        $stub = file_get_contents(base_path('packages/modular/stubs/Filament/Resources/Tables/StubTable.php'));

        $dir = dirname($tableFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $content = str_replace(
            [
                'StubModuleNamespace',
                'StubSubModulePrefix',
                'StubTableName',
                'StubTable'
            ],
            [
                'Modules',
                $resourceName,
                $modelName,
                $modelName . 'Table'
            ],
            $stub
        );

        $tableSchema = $this->generateTableSchema($columns, $layoutData);
        $content = str_replace('// Add your table columns here', $tableSchema, $content);

        // Generate Filters
        $filtersSchema = $this->generateTableFilters($layoutData);
        $content = str_replace('// Add your table filters here', $filtersSchema, $content);

        // Generate Actions
        $actionsSchema = $this->generateTableActions($layoutData);
        if (!empty($actionsSchema)) {
            // Replace default actions block
            $content = preg_replace(
                '/->actions\(\[\s*ActionGroup::make\(\[\s*EditAction::make\(\),\s*DeleteAction::make\(\),\s*\]\),\s*\]\)/s',
                "->actions([\n                " . $actionsSchema . "\n            ])",
                $content
            );
        }

        // Generate Header Actions
        $headerActionsSchema = $this->generateTableHeaderActions($layoutData);
        if (!empty($headerActionsSchema)) {
             // Add header actions if any (default stub doesn't have them usually, but we can append or inject if placeholder existed. 
             // Since stub doesn't have it, we might need to inject it before ->columns or at end of table method.
             // But wait, StubTable.php doesn't have headerActions placeholder. Let's assume user wants standard actions.
             // For now, let's focus on Row Actions as requested by user "Table layout da Column filter action lar".
        }

        file_put_contents($tableFile, $content);
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
            ->map(function ($columnData, $columnName) {
                // Handle both legacy (name => type) and new (name => [type, ...]) formats for backward compatibility
                // But since we updated callers to pass full objects, we should expect array.
                // However, createMigrationFile signature was not strictly typed for $columns content initially. 
                // Let's safe cast.
                
                $type = is_array($columnData) ? $columnData['type'] : $columnData;
                $props = is_array($columnData) ? $columnData : [];
                $name = $props['name'] ?? $columnName; // Use name from prop if available (createDB loop uses name index)
                
                // If the key is numeric (because we passed list of columns), name comes from prop
                // If key is string (legacy associative), name comes from key.
                if (is_numeric($columnName) && isset($props['name'])) {
                    $columnName = $props['name'];
                }

                $def = "";
                if ($type === 'foreignId') {
                    // Extract model from column name (e.g. user_id -> users) or use metadata if available
                    // For now, assuming simple foreignId('user_id')->constrained() or constrained('users')
                    $def = "\$table->foreignId('{$columnName}')->constrained()";
                } else {
                    $def = "\$table->{$type}('{$columnName}')";
                }

                // Add Modifiers
                if (!empty($props['nullable'])) {
                    $def .= "->nullable()";
                }
                if (!empty($props['unique'])) {
                    $def .= "->unique()";
                }
                if (!empty($props['index'])) {
                    $def .= "->index()";
                }

                return "            {$def};";
            })
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

        // Generate Casts
        $castsContent = collect($columns)
            ->filter(fn($data) => in_array(is_array($data) ? $data['type'] : $data, ['boolean', 'date', 'datetime', 'timestamp', 'json', 'array', 'object', 'collection']))
            ->map(function ($data, $column) {
                $type = is_array($data) ? $data['type'] : $data;
                $castType = match ($type) {
                    'boolean' => 'boolean',
                    'date' => 'date',
                    'datetime', 'timestamp' => 'datetime',
                    'json', 'jsonb' => 'array',
                    default => 'string',
                };
                return "        '{$column}' => '{$castType}',";
            })
            ->implode("\n");
        $castsContent = trim($castsContent, ",\n");

        $modelFileContent = str_replace('castsContent', $castsContent, $modelFileContent);

        // Generate relationships
        $relationships = "";
        foreach ($columns as $columnName => $data) {
            $columnType = is_array($data) ? $data['type'] : $data;
            
            if ($columnType === 'foreignId') {
                $metadata = is_array($data) ? $data : [];
                $specifiedModel = $metadata['related_model'] ?? null;

                // Infer relationship name: user_id -> user
                $relationName = Str::camel(str_replace('_id', '', $columnName));
                
                if ($specifiedModel) {
                    // Use FQCN directly if backslash present
                    if (str_contains($specifiedModel, '\\')) {
                         $relatedClass = "\\{$specifiedModel}";
                    } else {
                         // Fallback: Studly Singular
                         $relatedClass = Str::studly(Str::singular($specifiedModel));
                    }
                     $relationships .= "\n    public function {$relationName}(): \Illuminate\Database\Eloquent\Relations\BelongsTo\n    {\n        return \$this->belongsTo({$relatedClass}::class);\n    }\n";
                } else {
                    // Infer related model class: Studly Singular of relation name
                    $relatedModelName = Str::studly(Str::singular($relationName));
                    $relationships .= "\n    public function {$relationName}(): \Illuminate\Database\Eloquent\Relations\BelongsTo\n    {\n        return \$this->belongsTo({$relatedModelName}::class);\n    }\n";
                }
            }
        }
        
        // Helper to check if column exists
        $hasColumn = fn($name) => collect($columns)->contains(fn($c) => ($c['name'] ?? null) === $name || $c === $name);

        $bootLogic = "";
        
        $hasCreatedBy = $hasColumn('created_by');
        $hasUpdatedBy = $hasColumn('updated_by');
        $hasDeletedBy = $hasColumn('deleted_by');

        if ($hasCreatedBy || $hasUpdatedBy || $hasDeletedBy) {
            $bootLogic .= "\n    protected static function booted()\n    {\n";
            
            if ($hasCreatedBy) {
                $bootLogic .= "        static::creating(function (\$model) {\n            \$model->created_by = auth()->id();\n        });\n";
            }
            if ($hasUpdatedBy) {
                $bootLogic .= "        static::updating(function (\$model) {\n            \$model->updated_by = auth()->id();\n        });\n";
            }
            if ($hasDeletedBy) {
                 $bootLogic .= "        static::deleting(function (\$model) {\n            \$model->deleted_by = auth()->id();\n            \$model->saveQuietly();\n        });\n";
            }

            $bootLogic .= "    }\n";
        }

        // Append relationships before closing bracket
        // Current stub ends with }
        $modelFileContent = substr(trim($modelFileContent), 0, -1) . $bootLogic . $relationships . "\n}";

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

    public function getAvailableModels(): array
    {
        $models = [];
        $modulesPath = base_path('modules');

        if (!is_dir($modulesPath)) {
            return [];
        }

        $files = \Symfony\Component\Finder\Finder::create()
            ->in($modulesPath)
            ->files()
            ->name('*.php')
            ->path('src/Models')
            ->notPath('vendor'); // Exclusion just in case

        foreach ($files as $file) {
            $content = file_get_contents($file->getRealPath());
            
            // Extract Namespace
            if (preg_match('/namespace\s+(.+?);/', $content, $nsMatches)) {
                $namespace = $nsMatches[1];
                
                // Extract Class Name
                if (preg_match('/class\s+(\w+)/', $content, $classMatches)) {
                    $className = $classMatches[1];
                    $fullClass = $namespace . '\\' . $className;
                    
                    // Determine Group (Module Name)
                    // Path example: .../modules/core/user/src/Models/User.php
                    // Relative path: core/user/src/Models/User.php
                    $relativePath = $file->getRelativePathname();
                    
                    // Extract module part: core/user or region
                    // Remove /src/Models/User.php
                    $modulePath = str_replace('/src/Models/' . $file->getFilename(), '', $relativePath);
                    // Convert to title case for label: core/user -> Core/User
                    $groupLabel = Str::title(str_replace('/', ' / ', $modulePath));
                    
                    $models[$groupLabel][$fullClass] = $className; // Store as FQCN => Name
                }
            }
        }
        
        return $models;
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
            'foreignId' => 'foreignId',
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
            foreach ($columns as $name => $data) {
                // $data can be string (legacy) or array (new)
                // If string, it's just type. If array, it has type, name, etc.
                $type = is_array($data) ? $data['type'] : $data;
                $metadata = is_array($data) ? $data : [];
                $fields[] = $this->getFieldString($name, $type, $metadata);
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
                    } elseif ($colName) {
                        $dbColData = $columns[$colName] ?? [];
                        $dbColData = is_array($dbColData) ? $dbColData : [];
                        
                        $mergedMeta = array_merge($dbColData, $data);
                        $colType = $mergedMeta['type'] ?? 'string';
                        
                        $components[] = $this->getFieldString($colName, $colType, $mergedMeta);
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
                case 'split': // Split is handled similarly to Grid in Filament, or use Split component.
                // Filament Split component structure: Split::make([ Section::make(), Section::make() ])
                // But our builder uses generic 2-col structure.
                // If type is 'split', use Split::make([]). If 'grid', Grid::make().
                    $gridCols = $data['columns'] ?? 2;
                    $gridItems = $data['items'] ?? []; 
                    
                    $colComponents = [];
                    // Loop through columns
                    // Note: Split component expects an array of components, it distributes them. 
                    // Grid expects schema per column? No, Grid::make()->schema([ ...items... ]) uses columnSpan to place items?
                    // Actually Filament Grid: schema([...]) -> items flow. 
                    // My previous logic: Create Groups for each column.
                    
                    for ($i = 0; $i < $gridCols; $i++) {
                        $colBlocks = $gridItems[$i] ?? [];
                        if (empty($colBlocks)) {
                             // Empty column placeholder
                             if ($type === 'split') {
                                 // specific section for split?
                                 $colComponents[] = "\Filament\Forms\Components\Section::make([])"; 
                             } else {
                                 $colComponents[] = "\Filament\Forms\Components\Group::make()\n                        ->schema([])\n                        ->columnSpan(1)";
                             }
                             continue;
                        }
                        $colContent = $this->generateFormSchema($columns, $colBlocks);
                        
                        if ($type === 'split') {
                            $colComponents[] = "\Filament\Forms\Components\Section::make()\n                        ->schema([\n                            " . str_replace("\n", "\n    ", $colContent) . "\n                        ])";
                        } else {
                            $colComponents[] = "\Filament\Forms\Components\Group::make()\n                        ->schema([\n                            " . str_replace("\n", "\n    ", $colContent) . "\n                        ])\n                        ->columnSpan(1)";
                        }
                    }
                    
                    $gridContent = implode(",\n                        ", $colComponents);
                    
                    if ($type === 'split') {
                         $components[] = "\Filament\Forms\Components\Split::make([\n                        {$gridContent}\n                    ])";
                    } else {
                         $components[] = "\Filament\Schemas\Components\Grid::make({$gridCols})\n                    ->schema([\n                        {$gridContent}\n                    ])";
                    }
                    break;

                case 'wizard':
                    $steps = $data['steps'] ?? [];
                    $stepComponents = [];
                    foreach ($steps as $step) {
                        $stepLabel = $step['label'] ?? 'Step';
                        $stepBlocks = $step['schema'] ?? [];
                        $stepContent = $this->generateFormSchema($columns, $stepBlocks);
                        $stepComponents[] = "\Filament\Forms\Components\Wizard\Step::make('{$stepLabel}')\n                            ->schema([\n                                " . str_replace("\n", "\n        ", $stepContent) . "\n                            ])";
                    }
                    $stepsContentString = implode(",\n                        ", $stepComponents);
                    $components[] = "\Filament\Forms\Components\Wizard::make([\n                        {$stepsContentString}\n                    ])";
                    break;
            }
        }

        return implode(",\n                ", $components);
    }

    private function getFieldString($name, $type, $metadata = []): string
    {
        $label = Str::headline($name);
        
        // Relationship handling
        if ($type === 'foreignId') {
            $relatedModel = $metadata['related_model'] ?? null;
            $relatedColumn = $metadata['related_column'] ?? 'name';
            $isTranslatable = $metadata['is_translatable'] ?? false; // Check flag
            
            if (!$relatedModel) {
                 $relatedModel = Str::studly(str_replace('_id', '', $name));
            }
            
            $relationName = Str::camel(str_replace('_id', '', $name));

            // Custom query logic ONLY if translatable
            if ($isTranslatable) {
                return "\Filament\Forms\Components\Select::make('{$name}')\n                    ->label('{$label}')\n                    ->relationship('{$relationName}', '{$relatedColumn}', function (\$query) {\n                        return \$query->selectRaw(\"id, {$relatedColumn}->'\" . app()->getLocale() . \"' as {$relatedColumn}\")\n                            ->orderBy('{$relatedColumn}->>' . app()->getLocale(), 'desc');\n                    })\n                    ->searchable()\n                    ->preload()\n                    ->hidden(fn() => !auth()->user()->hasSuperAdmin())";
            } else {
                // Standard relationship
                return "\Filament\Forms\Components\Select::make('{$name}')\n                    ->label('{$label}')\n                    ->relationship('{$relationName}', '{$relatedColumn}')\n                    ->searchable()\n                    ->preload()";
            }
        }
        
        // Basic type mapping
        $field = "";
        
        switch ($type) {
            case 'boolean':
                $field = "\Filament\Forms\Components\Toggle::make('{$name}')\n                    ->label('{$label}')";
                break;
            case 'text':
            case 'mediumText':
            case 'longText':
            case 'json':
            case 'jsonb':
                $field = "\Filament\Forms\Components\Textarea::make('{$name}')\n                    ->label('{$label}')";
                break;
            case 'date':
                $field = "\Filament\Forms\Components\DatePicker::make('{$name}')\n                    ->label('{$label}')";
                break;
            case 'datetime':
            case 'timestamp':
                $field = "\Filament\Forms\Components\DateTimePicker::make('{$name}')\n                    ->label('{$label}')";
                break;
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
                $field = "\Filament\Forms\Components\TextInput::make('{$name}')\n                    ->label('{$label}')\n                    ->numeric()";
                break;
            default:
                $field = "\Filament\Forms\Components\TextInput::make('{$name}')\n                    ->label('{$label}')";
                break;
        }

        // Apply Common Metadata Modifiers
        if (!empty($metadata['required'])) {
            $field .= "\n                    ->required()";
        }
        if (!empty($metadata['placeholder'])) {
            $field .= "\n                    ->placeholder('{$metadata['placeholder']}')";
        }
        if (!empty($metadata['helper_text'])) {
            $field .= "\n                    ->helperText('{$metadata['helper_text']}')";
        }
        if (!empty($metadata['hint'])) {
            $field .= "\n                    ->hint('{$metadata['hint']}')";
        }
         if (!empty($metadata['default'])) {
            $field .= "\n                    ->default('{$metadata['default']}')";
        }
        if (!empty($metadata['column_span'])) {
            $span = $metadata['column_span'] === 'full' ? 'full' : $metadata['column_span'];
            $field .= "\n                    ->columnSpan('{$span}')";
        }

        return $field;
    }

    private function generateTableSchema(array $columns, array $builderBlocks): string
    {
        if (empty($builderBlocks['columns'] ?? [])) {
             // Fallback
             return "";
        }

        $components = [];

        foreach ($builderBlocks['columns'] as $col) {
             $name = $col['name'];
             $label = $col['label'] ?? Str::headline($name);
             $isSortable = $col['sortable'] ?? false ? '->sortable()' : '';
             $isSearchable = $col['searchable'] ?? false ? '->searchable()' : '';
             
             // Advanced Features
             $isTranslatable = $col['is_translatable'] ?? false;
             $relatedColumn = $col['related_column'] ?? 'name';

             if ($isTranslatable) {
                 if (($col['dbType'] ?? null) === 'foreignId' || str_ends_with($name, '_id')) {
                     // Translatable Relationship
                     $relationName = Str::camel(str_replace('_id', '', $name));
                     $nameField = "'{$relationName}.{$relatedColumn}.' . app()->getLocale()";
                 } else {
                     // Translatable Column
                     $nameField = "'{$name}.' . app()->getLocale()";
                 }
             } elseif (($col['dbType'] ?? null) === 'foreignId' || str_ends_with($name, '_id')) {
                 // Standard Relationship
                 $relationName = Str::camel(str_replace('_id', '', $name));
                 $nameField = "'{$relationName}.{$relatedColumn}'";
             } else {
                 // Standard Column
                 $nameField = "'{$name}'";
             }

             $type = $col['type'] ?? 'TextColumn';
             $cmp = "\Filament\Tables\Columns\\{$type}::make({$nameField})\n                    ->label('{$label}'){$isSortable}{$isSearchable}";
             
             $components[] = $cmp;
        }

        return implode(",\n                ", $components);
    }
    
    private function createLanguageFiles(string $commandName, array $translations)
    {
        if (empty($translations)) {
            return;
        }

        // Organize translations by locale
        // Input: 'Key' => ['en' => 'Value', 'ru' => 'Value']
        // Output: 'en' => ['Key' => 'Value'], 'ru' => ['Key' => 'Value']
        
        $localesData = [];
        
        foreach ($translations as $key => $locales) {
            foreach ($locales as $code => $value) {
                if (!empty($value)) {
                    $localesData[$code][$key] = $value;
                }
            }
        }

        $langDir = base_path("modules/{$commandName}/resources/lang");
        if (!is_dir($langDir)) {
            mkdir($langDir, 0755, true);
        }

        foreach ($localesData as $code => $keys) {
            $filePath = "{$langDir}/{$code}.json";
            
            // Merge with existing if file exists
            if (file_exists($filePath)) {
                $existing = json_decode(file_get_contents($filePath), true) ?? [];
                $keys = array_merge($existing, $keys);
            }

            file_put_contents($filePath, json_encode($keys, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }
    private function generateTableFilters(array $builderBlocks): string
    {
        if (empty($builderBlocks['filters'] ?? [])) {
            return "";
        }

        $components = [];

        foreach ($builderBlocks['filters'] as $filter) {
            $type = $filter['type'] ?? 'Filter';
            $column = $filter['column'] ?? 'id'; // Default to ID if not selected
            $label = $filter['label'] ?? Str::headline($column);

            switch ($type) {
                case 'SelectFilter':
                    $components[] = "\Filament\Tables\Filters\SelectFilter::make('{$column}')";
                    break;
                case 'TernaryFilter':
                    $components[] = "\Filament\Tables\Filters\TernaryFilter::make('{$column}')";
                    break;
                case 'Filter':
                default:
                    $components[] = "\Filament\Tables\Filters\Filter::make('{$column}')";
                    break;
            }
        }

        return implode(",\n                ", $components);
    }

    private function generateTableActions(array $builderBlocks): string
    {
        if (empty($builderBlocks['actions'] ?? [])) {
            // If empty, return default or empty? Stub has defaults. 
            // If user cleared them, we should probably return empty to respect "delete action" feature.
            return ""; 
        }

        $components = [];

        foreach ($builderBlocks['actions'] as $action) {
            $type = $action['type'] ?? 'EditAction';
            $components[] = "\Filament\Tables\Actions\\{$type}::make()";
        }

        return implode(",\n                ", $components);
    }

    private function generateTableHeaderActions(array $builderBlocks): string
    {
        if (empty($builderBlocks['header_actions'] ?? [])) {
            return "";
        }

        $components = [];

        foreach ($builderBlocks['header_actions'] as $action) {
            $type = $action['type'] ?? 'CreateAction';
            $components[] = "\Filament\Tables\Actions\\{$type}::make()";
        }

        return implode(",\n                ", $components);
    }
}

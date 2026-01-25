<?php

namespace Modules\MakerModule\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Modules\MakerModule\Filament\Resources\ModuleMakerResource\Pages;
use Modules\MakerModule\Models\Module;

class ModuleMakerResource extends Resource
{
    protected static ?string $model = Module::class;

    protected static ?string $slug = 'module-makers';
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-puzzle-piece';


    protected static string|null|\UnitEnum $navigationGroup = 'settings';
    public static function getNavigationGroup(): ?string
    {
        return __(parent::getNavigationGroup());
    }

    public static function getLabel(): ?string
    {
        return __('modules');
    }

    public static function getPluralLabel(): string
    {
        return __('modules');
    }



    public static function form(Schema $schema): Schema
    {
        $service = new \Modules\MakerModule\Services\ModuleMakerService();

        return $schema
            ->schema([

                Wizard::make([
                    Wizard\Step::make(__('Module Details'))
                        ->description(__('Basic information about the module'))
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    TextInput::make('name')
                                        ->label(__('Module Name'))
                                        ->required()
                                        ->placeholder('Blog')
                                        ->helperText(__('The name of the module, e.g. "Blog" or "E-Commerce/Orders"'))
                                        ->columnSpan(1),
                                    Select::make('group')
                                        ->label(__('Group'))
                                        ->options(collect(glob(base_path('modules/*')))
                                            ->filter(fn($directory) => is_dir($directory))
                                            ->map(fn($directory) => basename($directory))
                                            ->filter(fn($directory) => !is_dir(base_path("modules/{$directory}/src")))
                                            ->mapWithKeys(fn($directory) => [$directory => __('maker-module::modules.' . $directory)])
                                            ->toArray())
                                        ->helperText(__('Select an existing group or leave empty for root level'))
                                        ->columnSpan(1),
                                    Toggle::make('stable')
                                        ->label(__('Stable Version'))
                                        ->default(true)
                                        ->onColor('success')
                                        ->offColor('warning')
                                        ->hint(function (Get $get) {
                                            if ($get('stable')) {
                                                return __('Ready for production use.');
                                            }
                                            return __('Not recommended for production.');
                                        })
                                        ->columnSpan(1)
                                        ->live(),
                                ]),
                        ]),

                    Wizard\Step::make(__('Database Structure'))
                        ->description(__('Define tables and columns'))
                        ->icon('heroicon-o-table-cells')
                        ->schema([

                            \Filament\Forms\Components\ViewField::make('tables')
                                ->hiddenLabel()
                                ->view('maker-module::filament.forms.components.database-builder')
                                ->viewData([
                                    'availableModels' => $service->getAvailableModels(),
                                    'dataTypes' => $service->getDataTypes(null),
                                ])
                                ->columnSpanFull(),
                        ]),

                    Wizard\Step::make(__('Table Layouts'))
                        ->description(__('Configure table columns and actions'))
                        ->icon('heroicon-o-table-cells')
                        ->schema([
                            Repeater::make('table_layouts')
                                ->label(__('Table Layouts'))
                                ->schema([
                                    Select::make('table_name')
                                        ->label(__('Table'))
                                        ->options(function (Get $get, \Livewire\Component $livewire) {
                                            $tables = $get('tables') 
                                                ?? $get('../../tables') 
                                                ?? $get('../../../tables')
                                                ?? $livewire->data['tables'] // Try accessing root data directly
                                                ?? [];

                                            \Illuminate\Support\Facades\Log::info('TableOptions Debug', [
                                                'path_root' => count($get('tables') ?? []),
                                                'path_2' => count($get('../../tables') ?? []),
                                                'path_3' => count($get('../../../tables') ?? []),
                                                'livewire_data' => count($livewire->data['tables'] ?? []),
                                                'found' => count($tables),
                                                'first_item' => collect($tables)->first(),
                                            ]);
                                            
                                            // Ensure we work with array of arrays or array of objects
                                            return collect($tables)
                                                ->filter(function($t) {
                                                    // Handle both array and object (if casting issue)
                                                    $t = (array)$t;
                                                    return !empty($t['name']); 
                                                })
                                                ->pluck('name', 'name')
                                                ->toArray();
                                        })
                                        ->required()
                                        ->live(),

                                    \Filament\Forms\Components\ViewField::make('schema')
                                        ->label(function (Get $get) {
                                            $tables = $get('../../tables') ?? $get('../../../tables') ?? [];
                                            $tableName = $get('table_name');
                                            $cols = 0;
                                            if ($tableName) {
                                                $data = collect($tables)->firstWhere('name', $tableName);
                                                $cols = count($data['columns'] ?? []);
                                            }
                                            return __('table_builder') . " ({$cols} fields available)";
                                        })
                                        ->hiddenLabel(false)
                                        ->view('maker-module::filament.forms.components.table-builder')
                                        ->viewData(function (Get $get) {
                                            $tableName = $get('table_name');
                                            $tables = $get('../../tables') ?? $get('../../../tables') ?? $get('../../../../tables') ?? [];
                                            
                                            $columns = [];
                                            if ($tableName && !empty($tables)) {
                                                $tableData = collect($tables)->firstWhere('name', $tableName);
                                                if ($tableData) {
                                                    $columns = $tableData['columns'] ?? [];
                                                }
                                            }

                                            return [
                                                'columns' => array_values($columns),
                                            ];
                                        })
                                        ->key(function (Get $get) {
                                            $tableName = $get('table_name');
                                            $tables = $get('../../tables') ?? $get('../../../tables') ?? $get('../../../../tables') ?? [];
                                            
                                            if (!is_array($tables)) return 'tb-' . $tableName;

                                            $tableData = collect($tables)->firstWhere('name', $tableName);
                                            $columns = $tableData['columns'] ?? [];
                                            $colSignature = count($columns) . '-' . implode(',', array_column($columns, 'name'));
                                            return 'tb-' . $tableName . '-' . md5($colSignature);
                                        })
                                        ->columnSpanFull(),
                                ])
                                ->addActionLabel(__('Add Table Layout'))
                                ->columnSpanFull(),
                        ]),

                    Wizard\Step::make(__('Form Layouts'))
                        ->description(__('Drag and drop to configure form layout'))
                        ->icon('heroicon-o-squares-plus')
                        ->schema([
                            Repeater::make('resource_layouts')
                                ->label(__('Layouts'))
                                ->schema([
                                    Select::make('table_name')
                                        ->label(__('Table'))
                                        ->options(function (Get $get) {
                                            $tables = $get('../../tables') 
                                                ?? $get('../../../tables') 
                                                ?? $get('../../../../tables') 
                                                ?? $get('tables')
                                                ?? [];
                                            
                                            return collect($tables)
                                                ->where('has_resource', true)
                                                ->filter(fn($table) => !empty($table['name']))
                                                ->pluck('name', 'name')
                                                ->toArray();
                                        })
                                        ->required()
                                        ->live(),

                                    Grid::make(3)
                                        ->schema([
                                            TextInput::make('model_label')
                                                ->label(__('Model Label'))
                                                ->placeholder('Product'),
                                            TextInput::make('plural_model_label')
                                                ->label(__('Plural Model Label'))
                                                ->placeholder('Products'),
                                            TextInput::make('navigation_label')
                                                ->label(__('Navigation Label'))
                                                ->placeholder('Products'),
                                            TextInput::make('navigation_icon')
                                                ->label(__('Navigation Icon'))
                                                ->default('heroicon-o-rectangle-stack'),
                                            TextInput::make('navigation_group')
                                                ->label(__('Navigation Group'))
                                                ->placeholder('Shop'),
                                            TextInput::make('navigation_sort')
                                                ->label(__('Sort Order'))
                                                ->numeric()
                                                ->default(1),
                                        ]),

                                    \Filament\Forms\Components\ViewField::make('schema')
                                        ->label(function (Get $get) {
                                            $tables = $get('../../tables') ?? $get('../../../tables') ?? [];
                                            $tableName = $get('table_name');
                                            $cols = 0;
                                            if ($tableName) {
                                                $data = collect($tables)->firstWhere('name', $tableName);
                                                $cols = count($data['columns'] ?? []);
                                            }
                                            return __('visual_builder') . " (Debug: {$cols} columns found)";
                                        })
                                        ->hiddenLabel(false) // Show label for debug
                                        ->view('maker-module::filament.forms.components.visual-builder')
                                        ->viewData(function (Get $get) {
                                            $tableName = $get('table_name');
                                            // Try multiple paths to ensure we find the tables repeater state
                                            $tables = $get('../../tables') ?? $get('../../../tables') ?? $get('../../../../tables') ?? [];
                                            
                                            \Illuminate\Support\Facades\Log::info('VisualBuilder Debug', [
                                                'tableName' => $tableName,
                                                'tables_count' => count($tables),
                                                'tables_keys' => array_keys($tables),
                                                'first_table_dump' => !empty($tables) ? array_values($tables)[0] : 'empty',
                                            ]);

                                            $columns = [];
                                            if ($tableName && !empty($tables)) {
                                                $tableData = collect($tables)->firstWhere('name', $tableName);
                                                if ($tableData) {
                                                    $columns = $tableData['columns'] ?? [];
                                                }
                                            }

                                            return [
                                                'columns' => array_values($columns),
                                            ];
                                        })
                                        ->key(function (Get $get) {
                                            $tableName = $get('table_name');
                                            $tables = $get('../../tables') ?? $get('../../../tables') ?? $get('../../../../tables') ?? [];

                                            // Ensure tables is array
                                            if (!is_array($tables)) {
                                                return 'vb-' . $tableName;
                                            }

                                            $tableData = collect($tables)->firstWhere('name', $tableName);
                                            $columns = $tableData['columns'] ?? [];

                                            // Use robust checksum: table name + count + names
                                            $colSignature = count($columns) . '-' . implode(',', array_column($columns, 'name'));
                                            return 'vb-' . $tableName . '-' . md5($colSignature);
                                        })
                                        ->columnSpanFull(),
                                ])
                                ->addActionLabel(__('Add Layout Configuration'))
                                ->columnSpanFull(),
                        ]),
                ])
                    ->columnSpanFull()
                    ->skippable()
            ]);
    }


    public static function table(Table $table): Table
    {


        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('id'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('namespace')
                    ->label(__('namespace'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('path')
                    ->label(__('path'))
                    ->sortable()
                    ->searchable(),
                BadgeColumn::make('count_tables')
                    ->label(__('tables'))
                    ->getStateUsing(fn(Module $record) => $record->tables()->count())
                    ->colors([
                        'primary',
                    ]),
                BadgeColumn::make('status')
                    ->label(__('status'))
                    ->formatStateUsing(function ($state) {
                        return $state ? __('active') : __('inactive');
                    })
            ])
            ->filters([
                //
            ])
            ->actions([

            ])
            ->headerActions([

            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListModuleMakers::route('/'),
            'create' => Pages\CreateModuleMaker::route('/create'),
            'edit' => Pages\EditModuleMaker::route('/{record}/edit'),
        ];
    }


    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasSuperAdmin();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasSuperAdmin();
    }

}

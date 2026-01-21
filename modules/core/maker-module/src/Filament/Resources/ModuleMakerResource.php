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
    
                            Repeater::make('tables')
                                ->label(__('Tables'))
                                ->schema([
                                    Grid::make(4)
                                        ->schema([
                                            TextInput::make('name')
                                                ->label(__('Table Name'))
                                                ->placeholder(__('posts'))
                                                ->required()
                                                ->helperText(__('The name of the table, e.g. "posts"'))
                                                ->columnSpan(2)
                                                ->live(onBlur: true),
                                            
                                            Toggle::make('has_resource')
                                                ->label(__('Generate Resource'))
                                                ->default(false)
                                                ->onColor('success')
                                                ->offColor('gray')
                                                ->helperText(__('Create a Filament Resource for this table'))
                                                ->columnSpan(1)
                                                ->live(),
                                                
                                            Fieldset::make(__('Options'))
                                                ->schema([
                                                    Toggle::make('soft_deletes')
                                                        ->label(__('Soft Deletes'))
                                                        ->default(false),
                                                    Toggle::make('logged')
                                                        ->label(__('User Timestamps'))
                                                        ->default(false),
                                                    Toggle::make('status')
                                                        ->label(__('Status Column'))
                                                        ->default(true),
                                                ])
                                                ->columns(3)
                                                ->columnSpan(4),
                                        ]),

                                    Repeater::make('columns')
                                        ->label(__('Columns'))
                                        ->schema([
                                            Grid::make(2)
                                                ->schema([
                                                    TextInput::make('name')
                                                        ->label(__('Column Name'))
                                                        ->required()
                                                        ->placeholder('title')
                                                        ->live(onBlur: true),
                                                    Select::make('type')
                                                        ->label(__('Column Type'))
                                                        ->required()
                                                        ->searchable()
                                                        ->options($service->getDataTypes(null))
                                                        ->live(),
                                                    Select::make('related_model')
                                                        ->label(__('Related Model'))
                                                        ->placeholder('Select Model')
                                                        ->helperText(__('Model for relationship'))
                                                        ->required()
                                                        ->searchable()
                                                        ->options($service->getAvailableModels())
                                                        ->visible(fn (Get $get) => $get('type') === 'foreignId'),
                                                    TextInput::make('related_column')
                                                        ->label(__('Related Column'))
                                                        ->placeholder('name')
                                                        ->default('name')
                                                        ->helperText(__('Column to display in select (default: name)'))
                                                        ->required()
                                                        ->visible(fn (Get $get) => $get('type') === 'foreignId'),
                                                    
                                                    Grid::make(3)
                                                        ->schema([
                                                            \Filament\Forms\Components\Toggle::make('nullable')
                                                                ->label('Nullable')
                                                                ->inline(false),
                                                            \Filament\Forms\Components\Toggle::make('unique')
                                                                ->label('Unique')
                                                                ->inline(false),
                                                            \Filament\Forms\Components\Toggle::make('index')
                                                                ->label('Index')
                                                                ->inline(false),
                                                        ])->columnSpanFull(),
                                                ]),
                                        ])
                                        ->defaultItems(1)
                                        ->collapsible()
                                        ->columnSpanFull()
                                        ->live(),
                                ])
                                ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                ->collapsed(fn ($state) => count($state) > 1)
                                ->defaultItems(0)
                                ->addActionLabel(__('Add Table'))
                                ->reorderable(false)
                                ->live(), // Important for next step to see changes
                        ]),

                    Wizard\Step::make(__('Resource Layouts'))
                        ->description(__('Drag and drop to configure form layout'))
                        ->icon('heroicon-o-squares-plus')
                        ->schema([
                            Repeater::make('resource_layouts')
                                ->label(__('Layouts'))
                                ->schema([
                                    Select::make('table_name')
                                        ->label(__('Table'))
                                        ->options(function (Get $get) {
                                            $tables = $get('../../tables') ?? [];
                                            return collect($tables)
                                                ->where('has_resource', true)
                                                ->filter(fn($table) => !empty($table['name']))
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
                                            return __('visual_builder') . " (Debug: {$cols} columns found)";
                                        })
                                        ->hiddenLabel(false) // Show label for debug
                                        ->view('maker-module::filament.forms.components.visual-builder')
                                        ->viewData(function (Get $get) {
                                            $tableName = $get('table_name');
                                            // Try multiple paths to ensure we find the tables repeater state
                                            $tables = $get('../../tables') ?? $get('../../../tables') ?? [];
                                            
                                            $columns = [];
                                            if ($tableName) {
                                                $tableData = collect($tables)->firstWhere('name', $tableName);
                                                $columns = $tableData['columns'] ?? [];
                                            }

                                            return [
                                                'columns' => array_values($columns),
                                            ];
                                        })
                                        ->key(function (Get $get) {
                                            $tableName = $get('table_name');
                                            $tables = $get('../../tables') ?? $get('../../../tables') ?? [];
                                            
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


}

<?php

namespace Modules\MakerModule\Filament\Resources;


use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
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



    public static function form(Schema $form): Schema
    {
        $service = new \Modules\MakerModule\Services\ModuleMakerService();

        return $form
            ->schema([

                \Filament\Schemas\Components\Wizard::make([
                    \Filament\Schemas\Components\Wizard\Step::make(__('Module Details'))
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

                    \Filament\Schemas\Components\Wizard\Step::make(__('Database Structure'))
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
                                                ->columnSpan(2),
                                            
                                            Toggle::make('has_resource')
                                                ->label(__('Generate Resource'))
                                                ->default(false)
                                                ->onColor('success')
                                                ->offColor('gray')
                                                ->helperText(__('Create a Filament Resource for this table'))
                                                ->columnSpan(1),
                                                
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
                                                        ->placeholder('title'),
                                                    Select::make('type')
                                                        ->label(__('Column Type'))
                                                        ->required()
                                                        ->searchable()
                                                        ->options($service->getDataTypes(null)),
                                                ]),
                                        ])
                                        ->defaultItems(1)
                                        ->collapsible()
                                        ->columnSpanFull(),
                                ])
                                ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                ->collapsed(fn ($state) => count($state) > 1)
                                ->defaultItems(0)
                                ->addActionLabel(__('Add Table'))
                                ->reorderable(false),
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

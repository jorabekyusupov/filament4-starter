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
        function getDataTypes($search = null): array
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

        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('name'))
                    ->required(),
                Select::make('group')
                    ->label(__('group'))
                    ->options(collect(glob(base_path('modules/*')))
                        ->filter(fn($directory) => is_dir($directory))
                        ->map(fn($directory) => basename($directory))
                        ->filter(fn($directory) => !is_dir(base_path("modules/{$directory}/src")))
                        ->mapWithKeys(fn($directory) => [$directory => __('maker-module::modules.' . $directory)])
                        ->toArray()),
                Toggle::make('stable')
                    ->label(__('stable'))
                    ->default(true)
                    ->onColor('success')
                    ->offColor('warning')
                    ->hint(function (Get $get) {
                        if ($get('stable')) {
                            return __('This will make the module stable and ready for production use.');
                        }
                        return __('This will make the module unstable and not recommended for production use.');
                    })
                    ->columnSpanFull()
                    ->live(),

                Section::make(__('tables'))
                    ->collapsed()
                    ->columnSpanFull()

                    ->schema([
                        Repeater::make('tables')
                            ->columnSpanFull()
                            ->label(__('tables'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('name'))
                                    ->placeholder(__('Name')),
                                Toggle::make('has_resource')
                                    ->label(__('has_resource'))
                                    ->default(false)
                                    ->onColor('success')
                                    ->offColor('warning'),

                                Fieldset::make(__('options'))
                                    ->columns(3)
                                    ->schema([
                                        Toggle::make('soft_deletes')
                                            ->label(__('soft_deletes'))
                                            ->default(false)
                                            ->onColor('success')
                                            ->offColor('warning'),

                                        Toggle::make('logged')
                                            ->label(__('logged'))
                                            ->default(false)
                                            ->onColor('success')
                                            ->offColor('warning'),

                                        Toggle::make('status')
                                            ->label(__('status'))
                                            ->default(true)
                                            ->onColor('success')
                                            ->offColor('warning'),
                                    ]),

                                Repeater::make('columns')
                                    ->columnSpanFull()
                                    ->label(__('columns'))
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label(__('name'))
                                                    ->placeholder(__('Name')),
                                                Select::make('type')
                                                    ->label(__('type'))
                                                    ->searchable()
                                                    ->required()
                                                    ->getSearchResultsUsing(fn($search) => getDataTypes($search))
                                                    ->options(getDataTypes(null))
                                            ])
                                    ]),

                            ])
                            ->defaultItems(0)

                    ]),


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
//            'create' => Pages\CreateModuleMaker::route('/create'),
//            'edit' => Pages\EditModuleMaker::route('/{record}/edit'),
        ];
    }


    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }


}

<?php

namespace StubModuleNamespace\StubSubModulePrefix\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use StubModuleNamespace\StubSubModulePrefix\Models\StubTableName;

class StubTableNameResource extends Resource
{
    protected static ?string $model = StubTableName::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Add your form fields here
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Add your table columns here
            ])
            ->filters([
                // Add your table filters here
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make()
                ]),
            ])
            ->bulkActions([
                // Add your bulk actions here
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Add your relations here
        ];
    }

    public static function getPages(): array
    {
        return [
//            'index' => ListRecords::route('/'),
//            'create' => CreateRecord::route('/create'),
//            'view' => ViewRecord::route('/{record}'),
//            'edit' => EditRecord::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'StubTableNames';
    }

    public static function getPluralModelLabel(): string
    {
        return 'StubTableNames';
    }

    public static function getModelLabel(): string
    {
        return 'StubTableName';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Content';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-rectangle-stack';
    }

    public static function getRecordTitleAttribute(): ?string
    {
        return 'id';
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->id;
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            // Add search result details here
        ];
    }

    public static function getGlobalSearchResultActions($record): array
    {
        return [
            // Add global search result actions here
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            // Add globally searchable attributes here
        ];
    }

    public static function getDefaultTableSortColumn(): ?string
    {
        return 'created_at';
    }

    public static function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    public static function getTableRecordUrlUsing(): ?\Closure
    {
        return null;
    }

    public static function getTableRecordActionUsing(): ?\Closure
    {
        return null;
    }

    public static function getTableBulkActions(): array
    {
        return [
            // Add bulk actions here
        ];
    }

    public static function getTableFilters(): array
    {
        return [
            // Add table filters here
        ];
    }

    public static function getTableActions(): array
    {
        return [
            // Add table actions here
        ];
    }

    public static function getTableColumns(): array
    {
        return [
            // Add table columns here
        ];
    }

    public static function getFormSchema(): array
    {
        return [
            // Add form schema here
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getResource(): string
    {
        return static::class;
    }

    public static function getModel(): string
    {
        return static::$model;
    }

    public static function getResourcePages(): array
    {
        return static::getPages();
    }

    public static function getResourceRelations(): array
    {
        return static::getRelations();
    }

    public static function getResourceFormSchema(): array
    {
        return static::getFormSchema();
    }

    public static function getResourceTableColumns(): array
    {
        return static::getTableColumns();
    }

    public static function getResourceTableFilters(): array
    {
        return static::getTableFilters();
    }

    public static function getResourceTableActions(): array
    {
        return static::getTableActions();
    }

    public static function getResourceTableBulkActions(): array
    {
        return static::getTableBulkActions();
    }
}
<?php

namespace StubModuleNamespace\StubSubModulePrefix\Filament\Resources\StubTableNameResource;

use Filament\Schemas\Schema;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;

use StubModuleNamespace\StubSubModulePrefix\Filament\Resources\StubTableNameResource\Schemas\StubForm;
use StubModuleNamespace\StubSubModulePrefix\Filament\Resources\StubTableNameResource\Tables\StubTable;
use StubModuleNamespace\StubSubModulePrefix\Models\StubTableName;

class StubTableNameResource extends Resource
{
    protected static ?string $model = StubTableName::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|null|\UnitEnum $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return StubForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StubTable::table($table);
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

}
<?php

namespace StubModuleNamespace\StubSubModulePrefix\Filament\Resources\StubTableNameResource;

use Filament\Schemas\Schema;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;

use StubModuleNamespace\StubSubModulePrefix\Filament\Resources\StubTableNameResource\Schemas\StubForm;
use StubModuleNamespace\StubSubModulePrefix\Filament\Resources\StubTableNameResource\Tables\StubTable;
use StubModuleNamespace\StubSubModulePrefix\Filament\Resources\StubTableNameResource\Pages;
use StubModuleNamespace\StubSubModulePrefix\Models\StubTableName;

class StubTableNameResource extends Resource
{
    protected static ?string $model = StubTableName::class;

    protected static string | BackedEnum | null $navigationIcon = StubNavigationIcon;

    protected static string|null|\UnitEnum $navigationGroup = StubNavigationGroupProperty;

    protected static ?int $navigationSort = StubNavigationSort;

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
            'index' => Pages\ListStubTableName::route('/'),
            'create' => Pages\CreateStubTableName::route('/create'),
            'edit' => Pages\EditStubTableName::route('/{record}/edit'),
            'view' => Pages\ViewStubTableName::route('/{record}'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return StubNavigationLabel;
    }

    public static function getPluralModelLabel(): string
    {
        return StubPluralModelLabel;
    }

    public static function getModelLabel(): string
    {
        return StubModelLabel;
    }

    public static function getNavigationGroup(): ?string
    {
        return StubNavigationGroup;
    }

    public static function getNavigationSort(): ?int
    {
        return StubNavigationSort;
    }

    public static function getNavigationIcon(): ?string
    {
        return StubNavigationIcon;
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
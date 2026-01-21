<?php

namespace Modules\Order\Filament\Resources;

use Filament\Schemas\Schema;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

use Modules\Order\Models\Order;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|null|\UnitEnum $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('user_id')
                    ->label('User Id')
                    ->relationship('user', 'id')
                    ->searchable()
                    ->preload()
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
        return 'Orders';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Orders';
    }

    public static function getModelLabel(): string
    {
        return 'Order';
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
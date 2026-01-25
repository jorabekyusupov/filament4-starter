<?php

namespace StubModuleNamespace\StubSubModulePrefix\Filament\Resources\StubTableNameResource\Tables;

use Filament\Tables\Table;

class StubTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Add your table columns here
            ])
            ->filters([
                // Add your table filters here
            ])
            ->recordActions([
                StubRecordActions
            ])
            ->toolbarActions([
                StubBulkActions
            ]);
    }
}

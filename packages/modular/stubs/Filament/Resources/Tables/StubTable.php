<?php

namespace StubModuleNamespace\StubSubModulePrefix\Filament\Resources\StubTableNameResource\Tables;

use Filament\Tables\Table;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use StubModuleNamespace\StubSubModulePrefix\Models\StubTableName;

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
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

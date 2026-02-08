<?php

namespace Modules\RolePermission\Filament\Resources\RolePermissions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RolePermissionsTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->label(__('name_unique'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('translations.' . app()->getLocale())
                    ->label(__('name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('permissions_count')
                    ->label(__('permissions_count'))
                    ->sortable()
                    ->counts('permissions'),
                TextColumn::make('workspace.name.' . app()->getLocale())
                    ->label(__('workspace'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('created_at'))
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

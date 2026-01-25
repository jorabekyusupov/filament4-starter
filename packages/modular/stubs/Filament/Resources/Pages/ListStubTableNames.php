<?php


namespace StubModuleNamespace\StubSubModulePrefix\Filament\Resources\StubTableNameResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Filament\Actions\CreateAction;
use StubModuleNamespace\StubSubModulePrefix\Filament\Resources\StubTableNameResource\StubTableNameResource;

class ListStubTableName extends ListRecords
{
    protected static string $resource = StubTableNameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

  

    protected function getHeaderWidgets(): array
    {
        return [
            // Add header widgets here
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            // Add footer widgets here
        ];
    }
}
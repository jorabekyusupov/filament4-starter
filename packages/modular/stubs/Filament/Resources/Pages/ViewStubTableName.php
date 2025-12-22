<?php

namespace StubModuleNamespace\StubSubModulePrefix\Filament\Resources\StubTableNameResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use StubModuleNamespace\StubSubModulePrefix\Filament\Resources\StubTableNameResource;

class ViewStubTableName extends ViewRecord
{
    protected static string $resource = StubTableNameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
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
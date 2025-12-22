<?php

namespace StubModuleNamespace\StubSubModulePrefix\Filament\Resources\StubTableNameResource\Pages;

use Filament\Resources\Pages\EditRecord;
use StubModuleNamespace\StubSubModulePrefix\Filament\Resources\StubTableNameResource;

class EditStubTableName extends EditRecord
{
    protected static string $resource = StubTableNameResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'StubTableName updated successfully';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

    protected function afterSave(): void
    {
        // Add logic after record save
    }
}
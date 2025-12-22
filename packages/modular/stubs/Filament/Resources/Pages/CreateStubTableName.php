<?php


namespace StubModuleNamespace\StubSubModulePrefix\Filament\Resources\StubTableNameResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use StubModuleNamespace\StubSubModulePrefix\Filament\Resources\StubTableNameResource;

class CreateStubTableName extends CreateRecord
{
    protected static string $resource = StubTableNameResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'StubTableName created successfully';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    protected function afterCreate(): void
    {
        // Add logic after record creation
    }
}
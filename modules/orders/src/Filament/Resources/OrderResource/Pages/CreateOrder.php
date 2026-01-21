<?php


namespace Modules\Order\Filament\Resources\OrderResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Order\Filament\Resources\OrderResource;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Order created successfully';
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
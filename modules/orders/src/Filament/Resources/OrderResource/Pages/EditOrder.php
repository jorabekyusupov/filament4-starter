<?php

namespace Modules\Order\Filament\Resources\OrderResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Order\Filament\Resources\OrderResource;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Order updated successfully';
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
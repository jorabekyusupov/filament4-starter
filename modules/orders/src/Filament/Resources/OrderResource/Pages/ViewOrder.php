<?php

namespace Modules\Order\Filament\Resources\OrderResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Modules\Order\Filament\Resources\OrderResource;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

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
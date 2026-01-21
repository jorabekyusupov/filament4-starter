<?php


namespace Modules\Order\Filament\Resources\OrderResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Filament\Actions\CreateAction;
use Modules\Order\Filament\Resources\OrderResource;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            // Add more tabs here
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
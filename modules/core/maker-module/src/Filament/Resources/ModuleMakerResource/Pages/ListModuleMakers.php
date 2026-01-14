<?php

namespace Modules\MakerModule\Filament\Resources\ModuleMakerResource\Pages;


use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\MakerModule\Filament\Resources\ModuleMakerResource;

class ListModuleMakers extends ListRecords
{
    protected static string $resource = ModuleMakerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

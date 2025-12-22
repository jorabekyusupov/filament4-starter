<?php

namespace Modules\MakerModule\Filament\Resources\ModuleMakerResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\CreateRecord;
use Modules\MakerModule\Filament\Resources\ModuleMakerResource;

class CreateModuleMaker extends CreateRecord
{
    protected static string $resource = ModuleMakerResource::class;


    protected function getHeaderActions(): array
    {
        return [

        ];
    }

}

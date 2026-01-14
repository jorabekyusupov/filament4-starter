<?php

namespace Modules\MakerModule\Filament\Resources\ModuleMakerResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\CreateRecord;
use Modules\MakerModule\Filament\Resources\ModuleMakerResource;

class CreateModuleMaker extends CreateRecord
{
    protected static string $resource = ModuleMakerResource::class;



    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $service = new \Modules\MakerModule\Services\ModuleMakerService();
        return $service->create($data);
    }
}

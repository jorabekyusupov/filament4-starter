<?php

namespace Modules\MakerModule\Filament\Resources\ModuleMakerResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\MakerModule\Filament\Resources\ModuleMakerResource;

class EditModuleMaker extends EditRecord
{
    protected static string $resource = ModuleMakerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

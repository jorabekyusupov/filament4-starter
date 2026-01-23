<?php

namespace Modules\Application\Filament\Resources\Applications\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Application\Filament\Resources\Applications\ApplicationResource;

class CreateApplication extends CreateRecord
{
    protected static string $resource = ApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}

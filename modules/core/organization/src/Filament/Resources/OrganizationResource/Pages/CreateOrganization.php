<?php

namespace Modules\Organization\Filament\Resources\OrganizationResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Organization\Filament\Resources\OrganizationResource;

class CreateOrganization extends CreateRecord
{
    protected static string $resource = OrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}

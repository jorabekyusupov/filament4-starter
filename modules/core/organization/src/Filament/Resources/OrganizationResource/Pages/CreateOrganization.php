<?php

namespace Modules\Organization\Filament\Resources\OrganizationResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Organization\Filament\Resources\OrganizationResource;

class CreateOrganization extends CreateRecord
{
    use \Modules\Organization\Filament\Traits\ManagesPermissionSorting;

    protected static string $resource = OrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        $record = static::getModel()::create($data);
        $record->permissions()->sync($permissions);

        return $record;
    }
}

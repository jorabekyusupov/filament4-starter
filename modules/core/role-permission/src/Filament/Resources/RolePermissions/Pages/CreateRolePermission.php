<?php

namespace Modules\RolePermission\Filament\Resources\RolePermissions\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\RolePermission\Filament\Resources\RolePermissions\RolePermissionResource;

class CreateRolePermission extends CreateRecord
{
    use \Modules\RolePermission\Filament\Traits\ManagesPermissionSorting;

    protected static string $resource = RolePermissionResource::class;

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

<?php

namespace Modules\RolePermission\Filament\Resources\RolePermissions\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\RolePermission\Filament\Resources\RolePermissions\RolePermissionResource;

class EditRolePermission extends EditRecord
{
    use \Modules\RolePermission\Filament\Traits\ManagesPermissionSorting;

    protected static string $resource = RolePermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        $record->update($data);
        $record->permissions()->sync($permissions);

        return $record;
    }
}

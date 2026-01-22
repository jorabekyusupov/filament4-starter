<?php

namespace Modules\Organization\Filament\Resources\OrganizationResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Organization\Filament\Resources\OrganizationResource;

class EditOrganization extends EditRecord
{
    use \Modules\RolePermission\Filament\Traits\ManagesPermissionSorting;

    protected static string $resource = OrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $permissions = $data['permissions'] ?? [];
        $moderatorId = $data['moderator_id'] ?? null;
        unset($data['permissions'], $data['moderator_id']);

        $record->update($data);
        $record->syncPermissionsAndUpdateModeratorRole($permissions);

        if ($moderatorId) {
            $roleName = 'moderator_' . $record->id;
            $role = \Modules\RolePermission\Models\Role::where('name', $roleName)->first();
            if ($role) {
                $role->users()->sync([$moderatorId]);
            }
        }

        return $record;
    }
}

<?php

namespace Modules\Workspace\Filament\Resources\WorkspaceResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Workspace\Filament\Resources\WorkspaceResource;

class EditWorkspace extends EditRecord
{
    use \Modules\RolePermission\Filament\Traits\ManagesPermissionSorting;

    protected static string $resource = WorkspaceResource::class;

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

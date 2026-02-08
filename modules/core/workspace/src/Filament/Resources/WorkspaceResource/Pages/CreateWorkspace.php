<?php

namespace Modules\Workspace\Filament\Resources\WorkspaceResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Workspace\Filament\Resources\WorkspaceResource;
use Modules\User\Models\User;

class CreateWorkspace extends CreateRecord
{
    use \Modules\RolePermission\Filament\Traits\ManagesPermissionSorting;

    protected static string $resource = WorkspaceResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $permissions = $data['permissions'] ?? [];
        $moderatorId = $data['moderator_id'] ?? null;
        unset($data['permissions'], $data['moderator_id']);

        $record = static::getModel()::create($data);
        $record->syncPermissionsAndUpdateModeratorRole($permissions);
        User::query()
            ->find($moderatorId)
            ->update(['workspace_id' => $record->id]);
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

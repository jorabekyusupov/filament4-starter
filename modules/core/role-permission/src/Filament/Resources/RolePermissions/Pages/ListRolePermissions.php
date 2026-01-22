<?php

namespace Modules\RolePermission\Filament\Resources\RolePermissions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\RolePermission\Filament\Resources\RolePermissions\RolePermissionResource;

class ListRolePermissions extends ListRecords
{
    protected static string $resource = RolePermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

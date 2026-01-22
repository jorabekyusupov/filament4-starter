<?php

namespace Modules\RolePermission\Filament\Resources\Roles\Pages;

use Modules\RolePermission\Filament\Resources\Roles\RoleResource;
use BezhanSalleh\FilamentShield\Resources\Roles\Pages\ListRoles as BaseListRoles;

class ListRoles extends BaseListRoles
{
    protected static string $resource = RoleResource::class;
}
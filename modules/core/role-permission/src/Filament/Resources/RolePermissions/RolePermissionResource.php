<?php

namespace Modules\RolePermission\Filament\Resources\RolePermissions;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\RolePermission\Filament\Resources\RolePermissions\Schemas\RolePermissionForm;
use Modules\RolePermission\Filament\Resources\RolePermissions\Schemas\RolePermissionInfolist;
use Modules\RolePermission\Filament\Resources\RolePermissions\Tables\RolePermissionsTable;
use Modules\RolePermission\Models\Role;

class RolePermissionResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $slug = 'role-permissions';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return RolePermissionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RolePermissionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RolePermissionsTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRolePermissions::route('/'),
            'create' => Pages\CreateRolePermission::route('/create'),
            'edit' => Pages\EditRolePermission::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}

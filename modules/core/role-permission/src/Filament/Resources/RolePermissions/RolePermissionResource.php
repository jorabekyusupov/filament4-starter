<?php

namespace Modules\RolePermission\Filament\Resources\RolePermissions;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Workspace\Models\Workspace;
use Modules\RolePermission\Filament\Resources\RolePermissions\Schemas\RolePermissionForm;
use Modules\RolePermission\Filament\Resources\RolePermissions\Schemas\RolePermissionInfolist;
use Modules\RolePermission\Filament\Resources\RolePermissions\Tables\RolePermissionsTable;
use Modules\RolePermission\Models\Role;
use UnitEnum;

class RolePermissionResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $slug = 'role-permissions';

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-shield-check';

    /**
     * @return string|UnitEnum|null
     */
    public static function getNavigationGroup(): UnitEnum|string|null
    {
        return __('settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-shield::filament-shield.resource.label.roles');
    }

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

    public static function getEloquentQuery(): Builder
    {
        $defaultWorkspaceId = Workspace::query()->defaultId();
        return parent::getEloquentQuery()
            ->when(!auth()->user()->hasSuperAdmin(), function (Builder $query) {
                $query->where('workspace_id', auth()->user()->workspace_id);
            })
            ->where('workspace_id', '!=', $defaultWorkspaceId);
    }
}

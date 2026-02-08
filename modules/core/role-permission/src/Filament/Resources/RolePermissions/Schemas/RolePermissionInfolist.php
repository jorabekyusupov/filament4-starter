<?php

namespace Modules\RolePermission\Filament\Resources\RolePermissions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RolePermissionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('Id'),

                TextEntry::make('team_id')
                    ->label('Team Id'),

                TextEntry::make('name')
                    ->label('Name'),

                TextEntry::make('guard_name')
                    ->label('Guard Name'),

                TextEntry::make('workspace_id')
                    ->label(__('workspace_id')),

                TextEntry::make('created_at')
                    ->label('Created Date')
                    ->dateTime(),

                TextEntry::make('updated_at')
                    ->label('Last Modified Date')
                    ->dateTime(),
            ]);
    }
}

<?php

namespace Modules\RolePermission\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('team_id')
                    ->label('Team Id')
                    ->integer(),

                TextInput::make('name')
                    ->label('Name')
                    ->required(),

                TextInput::make('guard_name')
                    ->label('Guard Name')
                    ->required(),

                TextInput::make('workspace_id')
                    ->label(__('workspace_id'))
                    ->integer(),

                TextEntry::make('created_at')
                    ->label('Created Date')
                    ->dateTime(),

                TextEntry::make('updated_at')
                    ->label('Last Modified Date')
                    ->dateTime(),
            ]);
    }
}

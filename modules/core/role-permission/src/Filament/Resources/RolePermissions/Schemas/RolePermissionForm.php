<?php

namespace Modules\RolePermission\Filament\Resources\RolePermissions\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RolePermissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Hidden::make('guard_name')
                            ->default('web'),
                        TextInput::make('name')
                            ->label(__('name'))
                            ->required()
                            ->columnSpanFull()
                            ->hint(__('only_latins_characters_allowed'))
                            ->afterStateHydrated(function (TextInput $component, ?string $state) {
                                if ($state) {
                                    $component->state(preg_replace('/[^A-Za-z0-9_-]/', '', str_replace(' ', '_', $state)));
                                }
                            })
                            ->maxLength(255),
                        ...getNameInputsFilament('translations'),
                        \Filament\Forms\Components\ViewField::make('permissions')
                            ->label(__('permissions'))
                            ->columnSpanFull()
                            ->view('role-permission::filament.resources.role-permission-resource.components.permissions')
                            ->formatStateUsing(fn (?\Illuminate\Database\Eloquent\Model $record) => $record?->permissions->pluck('id')->toArray() ?? [])
                            ->dehydrated(true)
                    ])
                    ->columnSpanFull()
            ]);
    }
}

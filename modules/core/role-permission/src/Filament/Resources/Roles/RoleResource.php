<?php

namespace Modules\RolePermission\Filament\Resources\Roles;

use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource as BaseRoleResource;


use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Modules\RolePermission\Models\Role;

class RoleResource extends BaseRoleResource
{
    protected static ?string $model = Role::class;
    // URL manzili va menyudagi nomini belgilaymiz
    protected static ?string $slug = 'shield/roles';

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-shield-check';


    // Jadvalni (Table) o'zgartirish
    public static function table(Table $table): Table
    {
        // 1. Asl jadvalni chaqiramiz
        parent::table($table);

        // 2. Unga yangi ustun qo'shamiz
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('name_unique'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('translations.'.app()->getLocale())
                    ->label(__('name_'.app()->getLocale()))
                    ->searchable(),

                Tables\Columns\TextColumn::make('permissions_count')
                    ->label(__('permissions_count'))
                    ->counts('permissions'),
                Tables\Columns\TextColumn::make('users_count')
                    ->label(__('users_count'))
                    ->counts('users'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('created_at'))
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('workspace.name.'.app()->getLocale())
                    ->label(__('workspace'))
                    ->searchable(),

            ]);
    }

    // Sahifalarni (Pages) to'g'ri ulash
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return Role::query()
            ->with(['workspace']);

    }
    public static function canAccess(): bool
    {
        return auth()->user()->hasSuperAdmin();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasSuperAdmin();
    }
}

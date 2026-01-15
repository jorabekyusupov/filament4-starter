<?php

namespace Modules\Organization\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Organization\Filament\Resources\OrganizationResource\Pages;
use Modules\Organization\Models\Organization;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static ?string $slug = 'organizations';

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-user-group';

    protected static string|null|\UnitEnum $navigationGroup = 'settings';

    public static function getNavigationGroup(): ?string
    {
        return __('settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('organization');
    }

    public static function getLabel(): ?string
    {
        return __('organization');
    }

    public static function getPluralLabel(): ?string
    {
        return __('organizations');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                ...getNameInputsFilament(),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('id'))
                    ->sortable(),
                TextColumn::make('name.' . app()->getLocale())
                    ->label(__('name'))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return getWhereTranslationColumns($query, 'name', $search);
                    })
                    ->sortable(),

                TextColumn::make('users_count')
                    ->label(__('users_count'))
                    ->counts('users')
                    ->sortable(),
                ToggleColumn::make('status')
                    ->label(__('status'))
                    ->hidden(function () {
                        return !auth()->user()->hasSuperAdmin();
                    })
                    ->sortable(),
                ToggleColumn::make('is_dont_delete')
                    ->label(__('is_dont_delete'))
                    ->hidden(function () {
                        return !auth()->user()->hasSuperAdmin();
                    })
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ], FiltersLayout::AboveContent)
            ->recordActions([
                EditAction::make()
                    ->disabled(function (Model $record) {
                        return $record->is_dont_delete && !auth()->user()->hasSuperAdmin();
                    }),
                DeleteAction::make()
                    ->disabled(function (Model $record) {
                        return $record->is_dont_delete && !auth()->user()->hasSuperAdmin();
                    }),
                RestoreAction::make(),

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrganizations::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['users'])
            ->withoutHidden();
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['slug', 'structure.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->structure) {
            $details['Structure'] = $record->structure->name;
        }

        return $details;
    }


}

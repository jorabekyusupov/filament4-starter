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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\ViewField;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Organization\Filament\Resources\OrganizationResource\Pages;
use Modules\Organization\Models\Organization;
use Modules\User\Models\User;

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
                Select::make('moderator_id')
                    ->label(__('Moderator'))
                    ->options(function () {
                        $query = User::query()
                            ->select([
                                'id',
                                DB::raw("CONCAT_WS(' ', first_name, last_name, middle_name) AS full_name")
                            ])
                            ->when(!auth()->user()->hasSuperAdmin(), function (Builder $query) {
                                $query->where('organization_id', auth()->user()->organization_id);
                            })
                            ->limit(10);
                        return $query->pluck('full_name', 'id')->toArray();
                    })
                    ->getSearchResultsUsing(function (string $search) {
                        return User::query()
                            ->select([
                                'id',
                                DB::raw("CONCAT_WS(' ', first_name, last_name, middle_name) AS full_name")
                            ])
                            ->when(!auth()->user()->hasSuperAdmin(), function (Builder $query) {
                                $query->where('organization_id', auth()->user()->organization_id);
                            })
                            ->where(function (Builder $query) use ($search) {
                                $query->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('middle_name', 'like', "%{$search}%")
                                    ->orWhere('username', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%")
                                    ->orWhere('pin', 'like', "%{$search}%");
                            })
                            ->limit(10)
                            ->pluck('full_name', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->columnSpanFull()
                    ->createOptionForm([
                        TextInput::make('pin')
                            ->columnSpanFull()
                            ->mask(99999999999999)
                            ->minValue(10000000000000)
                            ->required()
                            ->placeholder('12345678901234')
                            ->numeric()
                            ->label(__('pinfl')),
                        \Filament\Schemas\Components\Grid::make(3)
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('first_name')
                                    ->label(__('first_name'))
                                    ->placeholder('Aziz')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(),
                                \Filament\Forms\Components\TextInput::make('last_name')
                                    ->placeholder('Azizov')
                                    ->label(__('last_name'))
                                    ->maxLength(255)
                                    ->live(),
                                \Filament\Forms\Components\TextInput::make('middle_name')
                                    ->placeholder('Azizovich')
                                    ->label(__('middle_name'))
                                    ->maxLength(255)
                                    ->live(),
                            ]),
                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('username')
                                    ->required()
                                    ->placeholder('aziz.azizov')
                                    ->maxLength(255)
                                    ->label(__('username'))
                                    ->default(function (callable $get) {
                                        $firstName = $get('first_name') ?? '';
                                        $lastName = $get('last_name') ?? '';
                                        if ($firstName && $lastName) {
                                            return strtolower($firstName . $lastName);
                                        }
                                        return '';
                                    })
                                    ->unique('users', 'username'),
                                \Filament\Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->placeholder('aziz@info.com')
                                    ->maxLength(255)
                                    ->label(__('email'))
                                    ->unique('users', 'email'),
                            ]),
                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('password')
                                    ->password()
                                    ->label(__('password'))
                                    ->placeholder('••••••••')
                                    ->minLength(8)
                                    ->maxLength(255)
                                    ->dehydrated(fn($state) => filled($state))
                                    ->required()
                                    ->same('password_confirmation'),
                                \Filament\Forms\Components\TextInput::make('password_confirmation')
                                    ->password()
                                    ->label(__('password_confirmation'))
                                    ->placeholder('••••••••')
                                    ->minLength(8)
                                    ->maxLength(255)
                                    ->dehydrated(false),

                            ]),
                    ])
                    ->createOptionUsing(function (array $data) {
                        $data = array_merge($data, [
                            'type' => 'moderator',
                        ]);
                        return \Modules\User\Models\User::create($data)->id;
                    })
                    ->formatStateUsing(function (?Model $record) {
                        if (!$record) return null;
                        $roleName = 'moderator_' . $record->id;
                        $role = \Modules\RolePermission\Models\Role::where('name', $roleName)->first();
                        return $role ? $role->users()->first()?->id : null;
                    })
                    ->dehydrated(false),
                Section::make(__('Permissions'))
                    ->schema([
                        ViewField::make('permissions')
                            ->label(__('permissions'))
                            ->view('role-permission::filament.resources.role-permission-resource.components.permissions')
                            ->formatStateUsing(fn(?Model $record) => $record?->permissions->pluck('id')->toArray() ?? [])
                            ->dehydrated(true)
                    ])
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed(),
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
                    ->using(function (Model $record, array $data): Model {
                        $permissions = $data['permissions'] ?? [];
                        unset($data['permissions']);
                        $record->update($data);
                        $record->syncPermissionsAndUpdateModeratorRole($permissions);
                        return $record;
                    })
                    ->disabled(function (Model $record) {
                        return ($record->is_dont_delete && !auth()->user()->hasSuperAdmin()) || $record->slug === 'default';
                    }),
                DeleteAction::make()
                    ->disabled(function (Model $record) {
                        return ($record->is_dont_delete && !auth()->user()->hasSuperAdmin()) || $record->slug === 'default';
                    }),
                RestoreAction::make(),

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrganizations::route('/'),
            'create' => Pages\CreateOrganization::route('/create'),
            'edit' => Pages\EditOrganization::route('/{record}/edit'),
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
        return ['slug'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];


        return $details;
    }


}

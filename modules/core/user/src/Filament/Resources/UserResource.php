<?php

namespace Modules\User\Filament\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Filament\Resources\UserResource\Pages;
use Modules\User\Models\User;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-users';

    protected static string|null|\UnitEnum $navigationGroup = 'settings';

    public static function getNavigationGroup(): ?string
    {
        return __(parent::getNavigationGroup());
    }

    public static function form(Schema $schema): Schema
    {

        return $schema
            ->schema([
                TextInput::make('pin')
                    ->columnSpanFull()
                    ->mask(99999999999999)
                    ->minValue(10000000000000)
                    ->required()
                    ->placeholder('12345678901234')
                    ->numeric()
                    ->label(__('pinfl')),

                Grid::make(3)
                    ->schema([
                        TextInput::make('first_name')
                            ->label(__('first_name'))
                            ->placeholder('Aziz')
                            ->required()
                            ->maxLength(255)
                            ->live(),
                        TextInput::make('last_name')
                            ->placeholder('Azizov')
                            ->label(__('last_name'))
                            ->maxLength(255)
                            ->live(),

                        TextInput::make('middle_name')
                            ->placeholder('Azizovich')
                            ->label(__('middle_name'))
                            ->maxLength(255)
                            ->live(),
                    ]),


                Grid::make(2)
                    ->schema([
                        TextInput::make('username')
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
                            ->unique('users', 'username', ignoreRecord: true),
                        TextInput::make('email')
                            ->email()
                            ->placeholder('aziz@info.com')
                            ->maxLength(255)
                            ->label(__('email'))
                            ->unique('users', 'email', ignoreRecord: true),
                    ]),
                Grid::make(2)
                    ->schema([
                        TextInput::make('password')
                            ->password()
                            ->label(__('password'))
                            ->placeholder('••••••••')
                            ->minLength(8)
                            ->maxLength(255)
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(?Model $record) => !$record)
                            ->same('password_confirmation'),
                        TextInput::make('password_confirmation')
                            ->password()
                            ->label(__('password_confirmation'))
                            ->placeholder('••••••••')
                            ->minLength(8)
                            ->maxLength(255)
                            ->dehydrated(false),
                    ]),
                Grid::make(2)
                    ->schema([
                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->label(__('role'))
                            ->preload()
                            ->searchable(),
                        Select::make('organization_id')
                            ->label(__('organization'))
                            ->relationship('organization', 'name', function (Builder $query) {
                                return $query->selectRaw("id, name->'" . app()->getLocale() . "' as name")
                                    ->orderBy('name->>' . app()->getLocale(), 'desc');
                            })
                            ->searchable()
                            ->hidden(fn() => !auth()->user()->hasSuperAdmin()),
                    ]),
                Hidden::make('type')
                    ->default('employee'),
                Hidden::make('organization_id')
                    ->default(fn() => auth()->user()->organization_id)
                    ->hidden(fn() => auth()->user()->hasSuperAdmin())
                    ->disabled(fn() => auth()->user()->hasSuperAdmin()),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('pin')
                    ->label(__('pinfl'))
                    ->sortable()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('first_name')
                    ->label(__('first_name'))
                    ->formatStateUsing(function ($record) {
                        return $record->last_name . " " . $record->first_name . " " . $record->middle_name;
                    })
                    ->searchable(
                        query: function (Builder $builder) {
                            $builder->whereRaw("CONCAT_WS(' ', last_name, first_name, middle_name) LIKE ?", ['%' . request('tableSearch') . '%']);
                        },
                        isIndividual: true
                    ),
                Tables\Columns\TextColumn::make('username')
                    ->label(__('username'))
                    ->sortable()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('role'))
                    ->sortable()
                    ->searchable(query: function (Builder $query, $search) {
                        return $query->whereHas('roles', function ($query) use ($search) {
                            return $query->where('name', 'like', '%' . $search . '%');
                        });
                    }, isIndividual: true),
                Tables\Columns\TextColumn::make('organization.name.' . app()->getLocale())
                    ->label(__('organization'))
                    ->sortable()
                    ->searchable(query: function (Builder $query, $search) {
                        return $query->whereHas('organization', function ($query) use ($search) {
                            return getWhereTranslationColumns($query, 'name', $search);
                        });
                    }, isIndividual: true)
                    ->hidden(fn() => !auth()->user()->hasSuperAdmin()),
                ToggleColumn::make('status')
                    ->label(__('status'))
                    ->onColor('success')
                    ->offColor('danger')
                    ->onIcon('heroicon-o-check')
                    ->offIcon('heroicon-o-x-mark')
                    ->disabled(fn(Model $record) => $record->dont_touch)
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),


                //
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('organization_id')
                    ->label(__('organization'))
                    ->relationship('organization', 'title', function (Builder $query) {
                        $query->selectRaw("id, name->'" . app()->getLocale() . "' as title")
                            ->orderBy('name->>' . app()->getLocale(), 'desc');
                    }),
                Tables\Filters\SelectFilter::make('roles')
                    ->label(__('role'))
                    ->relationship('roles', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),


            ], Tables\Enums\FiltersLayout::AboveContent)
            ->recordActions([
                EditAction::make()
                    ->disabled(fn(Model $record) => $record->dont_touch),
                DeleteAction::make()
                    ->disabled(fn(Model $record) => $record->dont_touch),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['organization'])
            ->when(!auth()->user()->hasSuperAdmin(), function (Builder $query) {
                $query->where('organization_id', auth()->user()->organization_id);
            });
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
//            'create' => Pages\CreateUser::route('/create'),
//            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string
    {
        return __('user');
    }

    public static function getPluralLabel(): ?string
    {
        return __('users');
    }


}

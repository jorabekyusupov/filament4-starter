<?php

namespace Modules\Language\Filament\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Language\Filament\Resources\LanguageResource\Pages;
use Modules\Language\Models\Language;

class LanguageResource extends Resource
{
    protected static ?string $model = Language::class;

    protected static ?string $slug = 'languages';

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-globe-alt';

    protected static string|null|\UnitEnum $navigationGroup = 'settings';

    public static function getNavigationGroup(): ?string
    {
        return __(parent::getNavigationGroup());
    }

    public static function getNavigationLabel(): string
    {
        return __('languages');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                ...getNameInputsFilament(),
                TextInput::make('code')
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->label(__('language_code'))
                    ->searchable()
                    ->sortable(),
                BadgeColumn::make('is_default')
                    ->label(__('default'))
                    ->colors([
                        'success' => static fn($state): bool => $state,
                        'danger' => static fn($state): bool => !$state,
                    ])
                    ->formatStateUsing(function ($state) {
                        return $state ? __('active') : __('inactive');
                    }),
                ToggleColumn::make('is_default')
                    ->label(__('default'))
                    ->disabled(fn(Language $record): bool => $record->is_default)
                    ->onColor('success')
                    ->offColor('danger')
                    ->onIcon('heroicon-o-check')
                    ->offIcon('heroicon-o-x-mark')
                    ->beforeStateUpdated(function ($record, $state) {
                        Language::query()->update(['is_default' => false]);
                    }),
                ToggleColumn::make('status')
                    ->label(__('status'))
                    ->onColor('success')
                    ->offColor('danger')
                    ->onIcon('heroicon-o-check')
                    ->offIcon('heroicon-o-x-mark')
            ])
            ->filters([
                TrashedFilter::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLanguages::route('/'),
//            'create' => Pages\CreateLanguage::route('/create'),
//            'edit' => Pages\EditLanguage::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}

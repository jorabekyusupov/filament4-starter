<?php

namespace Modules\Setting\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Setting\Models\Setting;
use Modules\Setting\Filament\Resources\SettingResource\Pages;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-cog-6-tooth';

    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }

    public static function getModelLabel(): string
    {
        return __('Setting');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Settings');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('Setting Details'))
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->label(__('Key'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabled(fn(?Setting $record) => $record?->is_locked)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('label')
                            ->label(__('Label'))
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Select::make('group')
                            ->label(__('Group'))
                            ->options([
                                'general' => __('General'),
                                'social' => __('Social'),
                                'mail' => __('Mail'),
                                'system' => __('System'),
                            ])
                            ->default('general')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('group')
                                    ->label(__('Group'))
                                    ->required(),
                            ])
                            ->createOptionUsing(fn(array $data) => $data['group'])
                            ->required(),

                        Forms\Components\Select::make('type')
                            ->label(__('Type'))
                            ->options([
                                'text' => __('Text'),
                                'textarea' => __('Text Area'),
                                'boolean' => __('Boolean (Toggle)'),
                                'select' => __('Select'),
                                'editor' => __('Rich Editor'),
                                'file' => __('File'),
                            ])
                            ->live()
                            ->required()
                            ->disabled(fn(?Setting $record) => $record?->is_locked),

                        Forms\Components\Toggle::make('is_locked')
                            ->label(__('Lock Setting'))
                            ->helperText(__('Prevent deletion or key modification'))
                            ->default(false)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make(__('Value'))
                    ->schema([
                        Forms\Components\TextInput::make('value_text')
                            ->label(__('Value'))
                            ->statePath('value')
                            ->visible(fn(Get $get) => $get('type') === 'text' || $get('type') === null),

                        Forms\Components\Textarea::make('value_textarea')
                            ->label(__('Value'))
                            ->statePath('value')
                            ->visible(fn(Get $get) => $get('type') === 'textarea'),

                        Forms\Components\RichEditor::make('value_editor')
                            ->label(__('Value'))
                            ->statePath('value')
                            ->visible(fn(Get $get) => $get('type') === 'editor'),

                        Forms\Components\Toggle::make('value_boolean')
                            ->label(__('Value'))
                            ->statePath('value')
                            ->visible(fn(Get $get) => $get('type') === 'boolean')
                        // Cast explicitly if needed, but model has no generic cast for value.
                        ,

                        Forms\Components\FileUpload::make('value_file')
                            ->label(__('Value'))
                            ->statePath('value')
                            ->visible(fn(Get $get) => $get('type') === 'file')
                            ->downloadable()
                            ->openable(),

                        Forms\Components\KeyValue::make('options')
                            ->label(__('Options (Key => Label)'))
                            ->visible(fn(Get $get) => $get('type') === 'select')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('value_select')
                            ->label(__('Value'))
                            ->statePath('value')
                            ->options(fn(Get $get) => $get('options') ?? [])
                            ->visible(fn(Get $get) => $get('type') === 'select'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')->label(__('Key'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('label')->label(__('Label'))->searchable(),
                Tables\Columns\TextColumn::make('value')->label(__('Value'))->limit(50),
                Tables\Columns\TextColumn::make('group')->label(__('Group'))->badge()->sortable(),
                Tables\Columns\TextColumn::make('type')->label(__('Type'))->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->label(__('Group'))
                    ->options([
                        'general' => __('General'),
                        'social' => __('Social'),
                        'mail' => __('Mail'),
                        'system' => __('System'),
                    ]),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->hidden(fn(Setting $record) => $record->is_locked),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
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

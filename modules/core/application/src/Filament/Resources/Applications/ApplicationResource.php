<?php

namespace Modules\Application\Filament\Resources\Applications;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Modules\Application\Filament\Resources\Applications\Schemas\ApplicationForm;
use Modules\Application\Filament\Resources\Applications\Schemas\ApplicationInfolist;
use Modules\Application\Filament\Resources\Applications\Tables\ApplicationsTable;
use Modules\Application\Models\Application;
use UnitEnum;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static ?string $slug = 'applications';
    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return 'heroicon-o-command-line';
    }

    /**
     * @return string|null
     */

    /**
     * @return string|null
     */
    public static function getLabel(): ?string
    {
        return __('application');
    }

    public static function getPluralLabel(): ?string
    {
        return __('applications');
    }

    public static function getModelLabel(): string
    {
        return __('application');
    }

    public static function getPluralModelLabel(): string
    {
        return __('applications');
    }

    public static function getNavigationLabel(): string
    {
        return __('applications');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('settings');
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ApplicationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ApplicationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ApplicationsTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplications::route('/'),
            'create' => Pages\CreateApplication::route('/create'),
            'edit' => Pages\EditApplication::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
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

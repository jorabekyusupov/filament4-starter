<?php

namespace Modules\ActivityLog\Filament\Resources\ActivityLogs;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\ActivityLog\Filament\Resources\ActivityLogs\Schemas\ActivityLogForm;
use Modules\ActivityLog\Filament\Resources\ActivityLogs\Schemas\ActivityLogInfolist;
use Modules\ActivityLog\Filament\Resources\ActivityLogs\Tables\ActivityLogsTable;

use Modules\ActivityLog\Models\CustomActivityModel;

class ActivityLogResource extends \AlizHarb\ActivityLog\Resources\ActivityLogs\ActivityLogResource
{
    protected static ?string $model = CustomActivityModel::class;

    /**
     * @return string|\UnitEnum|null
     */
    public static function getNavigationGroup(): string|null|\UnitEnum
    {
        return __('settings');
    }


    public static function getNavigationIcon(): string
    {
        return 'heroicon-s-clipboard-document-list';
    }


}

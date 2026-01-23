<?php

namespace Modules\ActivityLog\Filament\Resources\ActivityLogs\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\ActivityLog\Filament\Resources\ActivityLogs\ActivityLogResource;

class CreateActivityLog extends CreateRecord
{
    protected static string $resource = ActivityLogResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}

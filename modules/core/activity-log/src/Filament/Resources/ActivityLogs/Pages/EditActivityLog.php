<?php

namespace Modules\ActivityLog\Filament\Resources\ActivityLogs\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\ActivityLog\Filament\Resources\ActivityLogs\ActivityLogResource;

class EditActivityLog extends EditRecord
{
    protected static string $resource = ActivityLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

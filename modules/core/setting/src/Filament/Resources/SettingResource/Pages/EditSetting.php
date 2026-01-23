<?php

namespace Modules\Setting\Filament\Resources\SettingResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Setting\Filament\Resources\SettingResource;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->hidden(fn($record) => $record->is_locked),
        ];
    }
}

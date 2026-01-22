<?php

namespace Modules\RolePermission\Filament\Resources\Roles\Pages;

use Filament\Actions\DeleteAction;
use BezhanSalleh\FilamentShield\Resources\Roles\Pages\EditRole as EditRecord;
use Modules\RolePermission\Filament\Resources\Roles\RoleResource;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;


    protected function getHeaderActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getCancelFormAction(),
            DeleteAction::make(),
        ];
    }


    protected function getFormActions(): array
    {
        return [];
    }
}

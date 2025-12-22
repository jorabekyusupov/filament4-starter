<?php

namespace Modules\User\Filament\Resources\UserResource\Pages;

use Carbon\Carbon;
use Modules\User\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->mutateFormDataUsing(function ($data) {
                    $data['name'] = $data['last_name'] . ' ' . $data['first_name'] . ' ' . ($data['middle_name'] ?? '');
                    return $data;
                }),
        ];
    }
}

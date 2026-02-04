<?php

namespace  Modules\User\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;
use Modules\User\Filament\Resources\UserResource;
use Modules\User\Models\User;
use Modules\User\Services\UserCandidateSyncService;
use Throwable;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->modalWidth(Width::FiveExtraLarge),
        ];
    }
}

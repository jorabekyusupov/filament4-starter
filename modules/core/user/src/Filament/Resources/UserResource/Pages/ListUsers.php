<?php

namespace  Modules\User\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
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
                ->after(function (User $record, UserCandidateSyncService $syncService) {
                    try {
                        $response = $syncService->sync($record);
                        UserResource::notifyCandidateResponse($response);
                    } catch (Throwable $exception) {
                        report($exception);

                        Notification::make()
                            ->danger()
                            ->title(__('error'))
                            ->body(__('Failed to sync user with candidate service.'))
                            ->send();
                    }
                }),
        ];
    }
}

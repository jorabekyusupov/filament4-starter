<?php

namespace Modules\Application\Filament\Resources\Applications\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use Modules\Application\Models\Application;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;

class ApplicationsTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('username')
                    ->label(__('Username'))
                    ->icon('heroicon-o-user')
                    ->searchable(),

                TextColumn::make('webhook_url')
                    ->label(__('Webhook'))
                    ->limit(30)
                    ->copyable()
                    ->copyMessage(__('Webhook URL copied'))
                    ->icon('heroicon-o-link')
                    ->tooltip(fn(TextColumn $column): ?string => $column->getState()),

                TextColumn::make('created_at')
                    ->label(__('Created Date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label(__('Created Date') . ' (From)'),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label(__('Created Date') . ' (Until)'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'], fn($query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn($query, $date) => $query->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                    Action::make('regenerate_key')
                        ->label(__('Regenerate Key'))
                        ->icon('heroicon-o-key')
                        ->requiresConfirmation()
                        ->action(function (Application $record) {
                            $record->update(['secret_private_key' => \Illuminate\Support\Str::random(64)]);
                            Notification::make()
                                ->title(__('Key Regenerated'))
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

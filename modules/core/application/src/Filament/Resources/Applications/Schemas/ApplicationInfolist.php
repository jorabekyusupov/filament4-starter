<?php

namespace Modules\Application\Filament\Resources\Applications\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontFamily;

class ApplicationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('General Information'))
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('Name'))
                            ->weight('bold'),
                        TextEntry::make('username')
                            ->label(__('Username'))
                            ->icon('heroicon-o-user'),
                        TextEntry::make('created_at')
                            ->label(__('Created Date'))
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label(__('Last Modified Date'))
                            ->dateTime(),
                    ])->columns(2),
                Section::make(__('Integration Details'))
                    ->schema([
                        TextEntry::make('webhook_url')
                            ->label(__('Webhook URL'))
                            ->icon('heroicon-o-link')
                            ->copyable()
                            ->limit(50),

                        TextEntry::make('secret_private_key')
                            ->label(__('Secret Key'))
                            ->copyable()
                            ->obscured() // Or just hidden/masked if preferred, usually keys are sensitive
                            ->fontFamily(\Filament\Support\Enums\FontFamily::Mono),
                    ])->columns(2),
            ]);
    }
}

<?php

namespace Modules\Language\Filament\Resources\LanguageResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Language\Filament\Resources\LanguageResource;

class CreateLanguage extends CreateRecord
{
    protected static string $resource = LanguageResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}

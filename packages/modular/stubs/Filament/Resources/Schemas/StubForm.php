<?php

namespace StubModuleNamespace\StubSubModulePrefix\Filament\Resources\StubTableNameResource\Schemas;

use Filament\Schemas\Schema;
use StubModuleNamespace\StubSubModulePrefix\Models\StubTableName;

class StubForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Add your form fields here
            ]);
    }
}

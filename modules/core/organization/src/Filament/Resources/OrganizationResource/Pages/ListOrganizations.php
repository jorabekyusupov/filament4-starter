<?php

namespace Modules\Organization\Filament\Resources\OrganizationResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Organization\Filament\Resources\OrganizationResource;
use Modules\Structure\Models\Structure;
use Illuminate\Database\Eloquent\Model;

class ListOrganizations extends ListRecords
{
    protected static string $resource = OrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()

                ->using(function (array $data, string $model): Model {
                    $permissions = $data['permissions'] ?? [];
                    unset($data['permissions']);
                    
                    $strName = Structure::find($data['structure_id'])->name;
                    $data['name'] = [
                        'uz' => data_get($strName, 'uz') . ' ' . 'Ташкилоти',
                        'ru' => 'Организация ' . data_get($strName, 'ru'),
                        'en' => $data['structure_id'] . ' Organization',
                        'oz' => data_get($strName, 'oz') . ' ' . 'Tashkiloti',
                    ];
                    
                    $record = $model::create($data);
                    $record->permissions()->sync($permissions);
                    
                    return $record;
                }),
        ];
    }
}

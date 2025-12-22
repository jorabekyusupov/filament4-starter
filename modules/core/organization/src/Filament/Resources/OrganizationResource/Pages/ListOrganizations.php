<?php

namespace Modules\Organization\Filament\Resources\OrganizationResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Organization\Filament\Resources\OrganizationResource;
use Modules\Structure\Models\Structure;

class ListOrganizations extends ListRecords
{
    protected static string $resource = OrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()

                ->mutateFormDataUsing(function ($data) {
                    $strName = Structure::find($data['structure_id'])->name;
                    $data['name'] = [
                        'uz' => data_get($strName, 'uz') . ' ' . 'Ташкилоти',
                        'ru' => 'Организация ' . data_get($strName, 'ru'),
                        'en' => $data['structure_id'] . ' Organization',
                        'oz' => data_get($strName, 'oz') . ' ' . 'Tashkiloti',
                    ];
                    return $data;
                }),
        ];
    }
}

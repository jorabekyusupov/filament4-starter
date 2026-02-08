<?php

namespace Modules\Workspace\Filament\Resources\WorkspaceResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Workspace\Filament\Resources\WorkspaceResource;
use Modules\Structure\Models\Structure;
use Illuminate\Database\Eloquent\Model;

class ListWorkspaces extends ListRecords
{
    protected static string $resource = WorkspaceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()

                ->using(function (array $data, string $model): Model {
                    $permissions = $data['permissions'] ?? [];
                    unset($data['permissions']);
                    
                    $strName = Structure::find($data['structure_id'])->name;
                    $data['name'] = [
                        'uz' => data_get($strName, 'uz') . ' ' . 'Иш майдони',
                        'ru' => 'Рабочее пространство ' . data_get($strName, 'ru'),
                        'en' => $data['structure_id'] . ' Workspace',
                        'oz' => data_get($strName, 'oz') . ' ' . 'Ish maydoni',
                    ];
                    
                    $record = $model::create($data);
                    $record->permissions()->sync($permissions);
                    
                    return $record;
                }),
        ];
    }
}

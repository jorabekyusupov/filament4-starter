<?php

namespace Modules\App\Repositories;

use Illuminate\Support\Facades\DB;


interface BaseReadRepositoryInterface
{
    public function getActiveEntities(
        array   $columns = [],
        ?array   $where = [],
        ?string $searchByName = null,
        bool    $returnBuilder = false
    );

    public function getActiveEntitiesInCache(
        array   $columns = [],
        array   $where = [],
        ?string $searchByName = null
    );

    public function find(int $id, array $columns = ['*']);
}
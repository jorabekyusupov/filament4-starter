<?php

namespace Modules\App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;


class BaseReadRepository implements BaseReadRepositoryInterface
{


    public function getActiveEntities(
        array   $columns = [],
        ?array  $where = [],
        ?string $searchByName = null,
        bool    $returnBuilder = false
    )
    {
        $locale = app()->getLocale();
        $columns = $this->prepareColumns($columns, $locale);

        $query = $this->model
            ->newQuery()
            ->select($columns)
            ->when($where, fn($query) => $this->applyWhereConditions($query, $where))
            ->where('status', true);

        $query = getWhereTranslationColumns($query, 'name', $searchByName);

        return $returnBuilder ? $query : $query->get();
    }

    protected function prepareColumns(array $columns, string $locale): array
    {
        return empty($columns) ? [
            'id',
            DB::raw("name->>'$locale' as title"),
        ] : $columns;
    }

    protected function applyWhereConditions(Builder $query, array $where): Builder
    {
        foreach ($where as $field => $value) {
            if (is_array($value) && count($value) === 3) {
                [$field, $condition, $val] = $value;
                $query->where($field, $condition, $val);
            } else {
                $query->where($field, '=', $value);
            }
        }
        return $query;
    }

    public function getActiveEntitiesInCache(
        array   $columns = [],
        array   $where = [],
        ?string $searchByName = null
    )
    {
        $locale = app()->getLocale();
        $columns = empty($columns) ? [
            'id',
            DB::raw("name->>'$locale' as title"),
        ] : $columns;

        return cache()
            ->rememberForever($this->model->getTable() . '_active',
                function () use ($columns, $where) {
                    $this->getActiveEntities(
                        columns: $columns,
                        where: $where,
                        searchByName: null,
                        returnBuilder: false
                    );
                }
            );
    }



    public function find(int $id, array $columns = ['*'])
    {
        return $this->model->find($id, $columns);
    }
}

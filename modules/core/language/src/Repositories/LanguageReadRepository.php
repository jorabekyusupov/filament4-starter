<?php

namespace Modules\Language\Repositories;

use Modules\Language\Models\Language;
use Modules\Language\Repositories\LanguageReadRepositoryInterface;

class LanguageReadRepository implements LanguageReadRepositoryInterface
{

    public function __construct(
        protected Language $model
    )
    {
    }

    public function getActiveLanguages()
    {
        if(!config('app.start')){
            return collect([]);
        }
        return cache()
            ->rememberForever('languages', function () {
                return $this->model
                    ->newQuery()
                    ->select([
                        'id',
                        'name',
                        'code',
                        'is_default'
                    ])
                    ->activeLanguages()
                    ->get();
            });
    }

    public function findWithCode($code)
    {
        return $this->model
            ->newQuery()
            ->where('code', $code)
            ->first();
    }

    public function getAllLanguages($search = '')
    {
        return $this->model
            ->newQuery()
            ->select([
                'id',
                'name',
                'code',
                'is_default',
                'status',
                "created_at"
            ])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('code', 'like', '%' . $search . '%');
                });
            })
            ->get();

    }
}
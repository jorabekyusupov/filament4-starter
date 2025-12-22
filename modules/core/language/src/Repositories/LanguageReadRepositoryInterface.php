<?php

namespace Modules\Language\Repositories;

interface LanguageReadRepositoryInterface
{
    public function getActiveLanguages();

    public function findWithCode($code);

    public function getAllLanguages($search = '');

}
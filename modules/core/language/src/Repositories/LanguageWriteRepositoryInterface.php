<?php

namespace Modules\Language\Repositories;

interface LanguageWriteRepositoryInterface
{
    public function updateIsDefault($code): void;

}
<?php

namespace Modules\Language\Repositories;

use Modules\Language\Models\Language;
use Modules\Language\Repositories\LanguageWriteRepositoryInterface;

class LanguageWriteRepository implements LanguageWriteRepositoryInterface
{

    public function __construct(
        protected Language $language
    )
    {
    }

    public function updateIsDefault($code): void
    {
        $this->language->where('code', '!=', $code)->update(['is_default' => false]);
        $this->language->where('code', $code)->update(['is_default' => true]);
    }
}
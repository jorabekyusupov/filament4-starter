<?php

namespace Modules\Language\Observer;

use Modules\Language\Models\Language;

class LanguageObserver
{
    /**
     * @throws \JsonException
     */
    public function created(Language $language): void
    {
        // storage create new json file
        $path = storage_path('app/public/languages');
        if (!is_dir($path) && !mkdir($path, 0777, true) && !is_dir($path)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
        }
        $file = $path . '/' . $language->code . '.json';
        $langRu = base_path('lang/ru.json');
        $data = json_decode(file_get_contents($langRu), true, 512, JSON_THROW_ON_ERROR);
        $newData = array_map(static fn($value) => '', $data);
        file_put_contents($file, json_encode($newData, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
        cache()->forget('languages');

    }

    public function updated(Language $language): void
    {



    }

    public function deleted(Language $language): void
    {
    }

    public function restored(Language $language): void
    {
    }
}

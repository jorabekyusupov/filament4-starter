<?php

namespace Modules\Translate\Services;

use Illuminate\Support\Facades\File;
use JsonException;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class TranslationService
{
    /**
     * Build the table rows by unifying keys from all locale JSON files.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getTableRows(): array
    {
        $locales = getLocales();

        $translations = collect($locales)
            ->mapWithKeys(function (string $locale): array {
                return [$locale => $this->readLocaleFile($locale)];
            });

        $keys = $translations
            ->map(fn (array $items): array => array_keys($items))
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        return $keys
            ->map(function (string $key) use ($locales, $translations): array {
                $row = ['key' => $key];

                foreach ($locales as $locale) {
                    $row[$locale] = $translations[$locale][$key] ?? null;
                }

                return $row;
            })
            ->all();
    }

    public function updateTranslation(string $locale, string $key, ?string $value): void
    {
        $translations = $this->readLocaleFile($locale);
        $translations[$key] = $value ?? '';

        $this->writeLocaleFile($locale, $translations);
    }

    public function createZip(): BinaryFileResponse
    {
        $langPath = lang_path();
        $zipDirectory = storage_path('app/translations');

        File::ensureDirectoryExists($zipDirectory);

        $zipFilePath = $zipDirectory . DIRECTORY_SEPARATOR . 'translations_' . now()->format('Ymd_His') . '.zip';

        $zip = new ZipArchive();

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Unable to create translations archive.');
        }

        foreach (File::glob($langPath . DIRECTORY_SEPARATOR . '*.json') as $file) {
            $zip->addFile($file, basename($file));
        }

        $zip->close();

        return response()
            ->download($zipFilePath, 'translations.zip')
            ->deleteFileAfterSend(true);
    }

    private function readLocaleFile(string $locale): array
    {
        $path = $this->getLocalePath($locale);

        if (! File::exists($path)) {
            return [];
        }

        $contents = File::get($path);

        if ($contents === '') {
            return [];
        }

        try {
            $decoded = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException("Failed to decode translations for locale [{$locale}].", 0, $exception);
        }

        return is_array($decoded) ? $decoded : [];
    }

    private function writeLocaleFile(string $locale, array $translations): void
    {
        $path = $this->getLocalePath($locale);

        ksort($translations);

        $encoded = json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        if ($encoded === false) {
            throw new RuntimeException("Failed to encode translations for locale [{$locale}].");
        }

        File::put($path, $encoded . PHP_EOL, true);
    }

    private function getLocalePath(string $locale): string
    {
        return lang_path("{$locale}.json");
    }
}

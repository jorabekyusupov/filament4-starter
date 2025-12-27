<?php

declare(strict_types=1);

namespace Modules\Translate\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use JsonException;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class TranslationService
{
    public const MAIN_GROUP = 'main';

    /**
     * @var array<int, array{group: string, path: string}>
     */
    private array $translationSources = [];

    /**
     * Build the table rows by unifying keys from all locale JSON files.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getTableRows(): array
    {
        $locales = getLocales();

        return collect($this->getTranslationSources())
            ->flatMap(function (array $source) use ($locales) {
                $translations = collect($locales)
                    ->mapWithKeys(function (string $locale) use ($source): array {
                        return [$locale => $this->readLocaleFile($locale, $source['path'])];
                    });

                $keys = $translations
                    ->map(fn (array $items): array => array_keys($items))
                    ->flatten()
                    ->unique()
                    ->sort()
                    ->values();

                return $keys->map(function (string $key) use ($locales, $translations, $source): array {
                    $row = [
                        'group' => $source['group'],
                        'key' => $key,
                    ];

                    foreach ($locales as $locale) {
                        $row[$locale] = $translations[$locale][$key] ?? null;
                    }

                    return $row;
                });
            })
            ->values()
            ->all();
    }

    public function updateTranslation(string $locale, string $key, ?string $value, string $group = self::MAIN_GROUP): void
    {
        $path = $this->resolveGroupPath($group);
        $translations = $this->readLocaleFile($locale, $path);
        $translations[$key] = $value ?? '';

        $this->writeLocaleFile($locale, $translations, $path);
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

    /**
     * @return array<int, array{group: string, path: string}>
     */
    private function getTranslationSources(): array
    {
        if ($this->translationSources !== []) {
            return $this->translationSources;
        }

        $this->translationSources = collect([
            [
                'group' => self::MAIN_GROUP,
                'path' => lang_path(),
            ],
        ])
            ->concat($this->discoverModuleSources())
            ->unique('group')
            ->values()
            ->all();

        return $this->translationSources;
    }

    /**
     * @return array<int, array{group: string, path: string}>
     */
    private function discoverModuleSources(): array
    {
        $moduleRoots = collect(['modules', 'Modules'])
            ->map(fn (string $directory): string => base_path($directory))
            ->filter(fn (string $path): bool => File::isDirectory($path));

        if ($moduleRoots->isEmpty()) {
            return [];
        }

        return $moduleRoots
            ->flatMap(fn (string $root): array => File::directories($root))
            ->flatMap(function (string $layerPath): Collection {
                return collect(File::directories($layerPath))
                    ->map(function (string $modulePath): ?array {
                        $langPath = $modulePath . DIRECTORY_SEPARATOR . 'lang';

                        if (! File::isDirectory($langPath)) {
                            return null;
                        }

                        return [
                            'group' => basename($modulePath),
                            'path' => $langPath,
                        ];
                    })
                    ->filter()
                    ->values();
            })
            ->values()
            ->all();
    }

    private function resolveGroupPath(string $group): string
    {
        $path = collect($this->getTranslationSources())
            ->firstWhere('group', $group)['path'] ?? null;

        if ($path === null) {
            throw new RuntimeException("Unknown translation group [{$group}].");
        }

        return $path;
    }

    private function readLocaleFile(string $locale, string $directory): array
    {
        $path = $this->getLocalePath($locale, $directory);

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

    private function writeLocaleFile(string $locale, array $translations, string $directory): void
    {
        File::ensureDirectoryExists($directory);

        $path = $this->getLocalePath($locale, $directory);

        ksort($translations);

        $encoded = json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        if ($encoded === false) {
            throw new RuntimeException("Failed to encode translations for locale [{$locale}].");
        }

        File::put($path, $encoded . PHP_EOL, true);
    }

    private function getLocalePath(string $locale, string $directory): string
    {
        return $directory . DIRECTORY_SEPARATOR . "{$locale}.json";
    }
}

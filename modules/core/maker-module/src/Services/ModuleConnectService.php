<?php

namespace Modules\MakerModule\Services;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Modules\MakerModule\Models\Module;


class ModuleConnectService implements Plugin
{


    public function getId(): string
    {
        return 'filament-plugins';
    }

    public function register(Panel $panel): void
    {


        if (config('app.start')) {
            $modules = Module::query()
                ->where('status', true)
                ->when(app()->isProduction(), function ($query) {
                    $query->where('stable', true);
                })
                ->whereNull('deleted_at')
                ->get(['name', 'namespace', 'path']);



            foreach ($modules as $m) {
                $basePath = base_path(rtrim($m->path, '/') . '/src/Filament');
                $baseNs = rtrim($m->namespace, '\\') . '\\Filament';
                if ($m->isActive()) {
                    $resDir = $basePath . '/Resources';
                    if (\File::isDirectory($resDir)) {
                        $panel->discoverResources(
                            in: $resDir,
                            for: $baseNs . '\\Resources',
                        );
                    }
                    $pagesDir = $basePath . '/Pages';
                    if (\File::isDirectory($pagesDir)) {
                        $panel->discoverPages(
                            in: $pagesDir,
                            for: $baseNs . '\\Pages',
                        );
                    }
                    $clustersDir = $basePath . '/Clusters';
                    if (\File::isDirectory($clustersDir)) {
                        $panel->discoverClusters(
                            in: $clustersDir,
                            for: $baseNs . '\\Clusters',
                        );
                    }
                    $widgetsDir = $basePath . '/Widgets';
                    if (\File::isDirectory($widgetsDir)) {
                        $panel->discoverWidgets(
                            in: $widgetsDir,
                            for: $baseNs . '\\Widgets',
                        );
                    }
                }
            }
        }

    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return new static();
    }


}

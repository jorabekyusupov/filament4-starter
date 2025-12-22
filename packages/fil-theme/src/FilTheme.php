<?php

namespace Jora\FilTheme;

use Filament\Panel;
use Filament\Contracts\Plugin;
use Filament\Support\Assets\Theme;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentAsset;

class FilTheme implements Plugin
{
    /**
     * @var array<string, string>|string
     */
    public array|string $primaryColor = '#7367f0';
    public function getId(): string
    {
        return 'jora-fil-theme';
    }

    public static function make(): static
    {
        return new static;
    }

    public function register(Panel $panel): void
    {
        $panel
            ->colors([
                'primary' => $this->primaryColor,
            ])
            ->viteTheme('packages/fil-theme/resources/css/theme.css');
    }

    public function boot(Panel $panel): void
    {
        //
    }

    /**
     * Set the primary color.
     *
     * @param array<string, string>|string $color
     */
    public function primaryColor(array|string $color): static
    {
        $this->primaryColor = $color;

        return $this;
    }
}

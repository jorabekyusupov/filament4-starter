<?php

namespace App\Providers;


use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/rpc.php');


        $codes = config('app.locales');
        $labels = config('app.locale_labels');
        if (config('app.start')) {
            $codes = getLocales();
            $labels = getLocaleLabels();
        }
        LanguageSwitch::configureUsing(static function (LanguageSwitch $switch) use ($labels, $codes) {
            $switch
                ->locales($codes)
                ->labels(
                    $labels
                );
        });
    }
}

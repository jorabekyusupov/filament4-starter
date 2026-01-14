<?php

namespace App\Providers;


use BezhanSalleh\LanguageSwitch\Events\LocaleChanged;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Number;
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

        Event::listen(function (LocaleChanged $event) {
            $this->setLocaleCustom($event->locale);

        });

    }

    private function setLocaleCustom($locale): void
    {
        if ($locale === 'oz') {
            Number::useLocale('uz_Latn');
            Carbon::setLocale('uz_Latn');
            setlocale(LC_ALL, 'uz_UZ.utf8', 'uz_Latn', 'uz_UZ');
        }
        elseif ($locale === 'uz') {
            Number::useLocale('uz_Cyrl');
            Carbon::setLocale('uz_Cyrl');
            setlocale(LC_ALL, 'uz_UZ.utf8', 'uz_Cyrl', 'uz');
        }
    }
}

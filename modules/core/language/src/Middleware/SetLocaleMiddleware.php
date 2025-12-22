<?php

namespace Modules\Language\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Modules\Language\Repositories\LanguageReadRepositoryInterface;

class SetLocaleMiddleware
{
    public function handle(Request $request, Closure $next)
    {

        $locale = $request->segment(1);

        $allowedLanguages = app(LanguageReadRepositoryInterface::class)
            ->getActiveLanguages()
            ->pluck('code')
            ->toArray();
        if (in_array($locale, $allowedLanguages, true)) {
            Session::put('locale', $locale);
        }
        app()->setLocale(Session::get('locale', app()->getLocale()));
        return $next($request);
    }
}

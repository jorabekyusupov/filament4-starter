<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware
            ->alias([
                'basic-auth' => \Modules\Application\Middleware\BasicAuthMiddleware::class,
            ])
        ->appendToGroup('web', [
     \Modules\Language\Middleware\SetLocaleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (Throwable $e) {
            if (!app()->isProduction() && !app()->isLocal()) {
                app(\App\Services\ExceptionSenderService::class)->errorSend($e);
            }
        });
        //
    })
    ->create();

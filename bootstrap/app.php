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
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->booting(function ($app) {
        // Paksa storage path ke /tmp di lingkungan serverless/Vercel
        if (env('VERCEL') || isset($_ENV['VERCEL'])) {
            $app->useStoragePath('/tmp');
        }
    })
    ->create();
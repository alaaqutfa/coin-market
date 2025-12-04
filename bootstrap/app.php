<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
        $middleware->alias([
            'cors' => \Illuminate\Http\Middleware\HandleCors::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'superadmin' => \App\Http\Middleware\SuperAdminOnly::class,
            'mobile' => \App\Http\Middleware\MobileModeMiddleware::class,
        ]);

        // إستثناءات CSRF لـ API
        $middleware->validateCsrfTokens(except: [
            'api/*',
            'login',
            'logout',
            'sanctum/csrf-cookie'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function ($schedule) {
        $schedule->command('attendance:calculate-daily-hours')
            ->timezone('Asia/Beirut')
            ->dailyAt('02:00')
            ->withoutOverlapping();
    })
    ->create();

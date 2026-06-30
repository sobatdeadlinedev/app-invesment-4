<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Alias untuk Spatie Permission
        $middleware->alias([
            'role'               => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        // Tambahkan SetLocale middleware ke web group
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withCommands([
        \App\Console\Commands\ExpireDeposits::class,
    ])
    ->withSchedule(function (Schedule $schedule) {
        // Auto-expire deposit yang melewati batas waktu 1 jam — cek setiap menit
        $schedule->command('deposits:expire')->everyMinute();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
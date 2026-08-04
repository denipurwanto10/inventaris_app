<?php

use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\SyncNotifications;
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
        // 'admin' guards routes that only an Admin role may access
        // (Gudang & Pengguna management) — see routes/web.php.
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
        ]);

        // Runs on every authenticated web request:
        // - EnsureUserIsActive: logs out accounts an admin has deactivated.
        // - SyncNotifications: generates low-stock / due-date / pending-approval
        //   alerts and shares the unread count with the notification bell.
        $middleware->appendToGroup('web', [
            EnsureUserIsActive::class,
            SyncNotifications::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

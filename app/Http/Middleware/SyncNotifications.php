<?php

namespace App\Http\Middleware;

use App\Models\AppNotification;
use App\Support\Notifier;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SyncNotifications
{
    /**
     * Runs on every authenticated page: generates any new alerts (low
     * stock, loans due soon, pending approvals) and shares the unread
     * bell count + latest items with every view via layouts.app.
     * Register in bootstrap/app.php — see README.md, "Notifikasi".
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            Notifier::syncAll();

            View::share('unreadNotifCount', AppNotification::unread()->count());
            View::share('latestNotifs', AppNotification::latest()->limit(6)->get());
        }

        return $next($request);
    }
}

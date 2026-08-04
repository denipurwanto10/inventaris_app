<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Support\Notifier;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        Notifier::syncAll();

        $notifications = AppNotification::when($request->filter === 'unread', fn ($q) => $q->unread())
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(AppNotification $notification)
    {
        $notification->markRead();

        return $notification->url
            ? redirect($notification->url)
            : back();
    }

    public function markAllRead()
    {
        AppNotification::unread()->update(['read_at' => now()]);

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}

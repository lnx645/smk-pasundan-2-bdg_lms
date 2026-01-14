<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notif = Auth::user()->notifications;
        Auth::user()->unreadNotifications->markAsRead();
        return inertia('notifications/index', [
            'notifications' => $notif,
        ]);
    }
}

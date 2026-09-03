<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function all(Request $request) {
        return $request->user()->notifications;
    }

    public function unread(Request $request) {
        return $request->user()->unreadNotifications;
    }

    public function read(Request $request) {
        return $request->user()->readNotifications;
    }

    public function markAsRead(Request $request) {
        return $request->user()->unreadNotifications->markAsRead();
    }

    public function delete(Request $request) {
        return $request->user()->notifications()->delete();
    }
}

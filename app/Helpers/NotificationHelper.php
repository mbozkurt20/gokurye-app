<?php

namespace App\Helpers;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Pusher\Pusher;

class NotificationHelper
{
    static function add($data)
    {
        $data['admin_id'] = $data['admin_id'] ?? Auth::guard('admin')->id();

        if (!$data['admin_id']) {
            return;
        }

        $notification = \App\Models\Notification::create($data);
        $adminId = $data['admin_id'];

        try {
            $pusher = new Pusher(
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_ID'),
                ['cluster' => 'mt1', 'useTLS' => true]
            );

            $pusher->trigger(
                'notifications-' . $adminId,
                'new-notify-' . $adminId,
                array_merge($data, ['id' => $notification->id])
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('NotificationHelper Pusher hatası: ' . $e->getMessage());
        }
    }
}

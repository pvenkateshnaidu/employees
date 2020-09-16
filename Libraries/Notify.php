<?php

namespace App\Libraries;

class Notify
{

    public static function unreadNotifications()
    {
        $user = \Auth::user()->id;

        $notifications = \App\Models\Notification::where('notifiable_id', $user)->whereNull('read_at')->orderBy('created_at', 'DESC')->get();
        return $notifications;
    }

}

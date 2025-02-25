<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationList extends Component
{
    public $user_notifications = [];

    public function set_is_read($notification_id)
    {
        Auth::user()->unreadNotifications->where('id', $notification_id)
            ->markAsRead();
    }

    public function render()
    {
        $this->user_notifications = Auth::user()->notifications()
            ->select(['notifications.*', 'announcements.id AS announcement_id', 'announcements.title', 'announcements.slug', 'announcements.description'])
            ->join('announcements', 'announcements.id', 'data->announcement_id')
            ->limit(15)
            ->get();

        return view('livewire.notification-list');
    }
}

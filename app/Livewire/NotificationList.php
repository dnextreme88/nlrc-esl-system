<?php

namespace App\Livewire;

use App\Helpers\Helpers;
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

    public function mount()
    {
        $notifications_of_user = Auth::user()->notifications()
            ->limit(15)
            ->get();

        $this->user_notifications = Helpers::get_notifications($notifications_of_user);
    }

    public function render()
    {
        return view('livewire.notification-list');
    }
}

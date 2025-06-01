<?php

namespace App\Livewire;

use App\Helpers\Helpers;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationList extends Component
{
    public bool $is_student_role;
    public bool $is_teacher_role;
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

        $this->is_student_role = Helpers::is_student_role();
        $this->is_teacher_role = Helpers::is_teacher_role();
        $this->user_notifications = Helpers::get_notifications($notifications_of_user);
    }

    public function render()
    {
        return view('livewire.notification-list');
    }
}

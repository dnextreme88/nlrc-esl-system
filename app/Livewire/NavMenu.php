<?php

namespace App\Livewire;

use App\Helpers\Helpers;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

// Using NavigationMenu is conflicting with another class of the same name from Laravel Jetstream
class NavMenu extends Component
{
    public bool $is_admin_role;
    public bool $is_student_role;
    public bool $is_teacher_role;
    public $user_notifications = [];
    public $user_notifications_unread_count = 0;
    public $user_notifications_unread_count_is_overlap = false;
    public $userId;

    // Listen to an event
    #[On('echo-private:receive-announcement.{userId},\App\Events\ReceiveAnnouncementEvent')]
    #[On('echo-private:receive-meeting-booked.{userId},\App\Events\ReceiveMeetingBookedEvent')]
    public function onReceiveNotification($event)
    {
        Toaster::info('You have received a new notification!');

        $this->get_notifications();
    }

    public function get_notifications()
    {
        $this->user_notifications_unread_count = Auth::user()->unreadNotifications
            ->count();

        $notifications_of_user = Auth::user()->notifications()
            ->limit(5)
            ->get();

        $this->user_notifications = Helpers::get_notifications($notifications_of_user);

        $notifications_unread_on_screen_count = $notifications_of_user->filter(fn ($notif) => $notif->read_at == null)->count();

        if ($this->user_notifications_unread_count > $notifications_unread_on_screen_count) {
            $this->user_notifications_unread_count_is_overlap = true;
        }
    }

    public function mount()
    {
        $this->userId = Auth::user()->id;
        $this->is_admin_role = Helpers::is_admin_role();
        $this->is_student_role = Helpers::is_student_role();
        $this->is_teacher_role = Helpers::is_teacher_role();
    }

    #[On('mark-is-read')]
    public function render()
    {
        $this->get_notifications();

        return view('livewire.nav-menu');
    }

    public function set_is_read($notification_id)
    {
        Auth::user()->unreadNotifications->where('id', $notification_id)
            ->markAsRead();

        $this->dispatch('mark-is-read');
    }
}

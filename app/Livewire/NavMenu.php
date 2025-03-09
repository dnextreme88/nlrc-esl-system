<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

// Using NavigationMenu is conflicting with another class of the same name from Laravel Jetstream
class NavMenu extends Component
{
    public $user_notifications = [];
    public $user_notifications_unread_count = 0;
    public $user_notifications_unread_count_is_overlap = false;
    public $userId;

    // Listen to an event
    #[On('echo-private:receive-announcement.{userId},\App\Events\ReceiveAnnouncementEvent')]
    public function onReceiveAnnouncement($event)
    {
        $this->get_notifications();
    }

    public function get_notifications()
    {
        $this->user_notifications_unread_count = Auth::user()->unreadNotifications
            ->count();

        $this->user_notifications = Auth::user()->notifications()
            ->select(['notifications.*', 'announcements.id AS announcement_id', 'announcements.title', 'announcements.slug', 'announcements.description'])
            ->join('announcements', 'announcements.id', 'data->announcement_id')
            ->limit(5)
            ->get();

        $notifications_unread_on_screen_count = $this->user_notifications->filter(fn ($notif) => $notif->read_at == null)->count();

        if ($this->user_notifications_unread_count > $notifications_unread_on_screen_count) {
            $this->user_notifications_unread_count_is_overlap = true;
        }
    }

    public function mount()
    {
        $this->userId = Auth::user()->id;
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

<?php

namespace App\Livewire\Announcements;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MiniAnnouncements extends Component
{
    public $latest_announcement;
    public $recent_announcements;

    public function set_is_read($notification_id): void
    {
        Auth::user()->unreadNotifications->where('id', $notification_id)
            ->markAsRead();
    }

    public function render()
    {
        $this->latest_announcement = Auth::user()->notifications()
            ->select(['notifications.*', 'announcements.id AS announcement_id', 'announcements.title', 'announcements.slug', 'announcements.description'])
            ->join('announcements', 'announcements.id', 'data->announcement_id')
            ->limit(1)
            ->first();

        if ($this->latest_announcement) {
            $this->recent_announcements = Auth::user()->notifications()
                ->select(['notifications.*', 'announcements.id AS announcement_id', 'announcements.title', 'announcements.slug', 'announcements.description'])
                ->join('announcements', 'announcements.id', 'data->announcement_id')
                ->whereNot('notifications.id', $this->latest_announcement->id)
                ->limit(5)
                ->get();
        }

        return view('livewire.announcements.mini-announcements');
    }
}

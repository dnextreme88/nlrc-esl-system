<?php

namespace App\Livewire\Announcements;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AnnouncementList extends Component
{
    use WithPagination;

    public function set_is_read($notification_id): void
    {
        Auth::user()->unreadNotifications->where('id', $notification_id)
            ->markAsRead();
    }

    public function render()
    {
        $announcements = Auth::user()->notifications()
            ->select(['notifications.*', 'announcements.id AS announcement_id', 'announcements.title', 'announcements.slug', 'announcements.description', 'announcements.tags'])
            ->join('announcements', 'announcements.id', 'data->announcement_id')
            ->where('type', 'announcement-sent')
            ->paginate(5);

        return view('livewire.announcements.announcement-list', compact('announcements'));
    }
}

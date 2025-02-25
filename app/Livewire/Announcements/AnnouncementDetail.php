<?php

namespace App\Livewire\Announcements;

use App\Models\Announcement;
use Livewire\Component;

class AnnouncementDetail extends Component
{
    public $current_announcement;

    public function mount($id, $slug)
    {
        $this->current_announcement = Announcement::where('id', $id)->where('slug', $slug)
            ->first();
    }

    public function render()
    {
        // TODO: TO ADD MODEL POLICIES SO THAT USERS WHO ARE SUPPOSED TO RECEIVE SPECIFIC ANNOUNCEMENTS
        // CAN RECEIVE THEM INSTEAD OF LETTING THEM VIEW ALL ANNOUNCEMENTS
        if ($this->current_announcement) {
            return view('livewire.announcements.announcement-detail');
        } else {
            abort(404);
        }
    }
}

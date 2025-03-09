<?php

namespace App\Livewire\Announcements;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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
        $user = User::findOrFail(Auth::user()->id);

        if ($this->current_announcement) {
            if ($user->cannot('view', $this->current_announcement)) {
                abort(403);
            } else {
                return view('livewire.announcements.announcement-detail');
            }
        } else {
            abort(404);
        }
    }
}

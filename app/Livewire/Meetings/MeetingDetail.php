<?php

namespace App\Livewire\Meetings;

use App\Enums\MeetingStatuses;
use App\Enums\Roles;
use App\Helpers\Helpers;
use App\Models\MeetingSlot;
use App\Models\MeetingSlotsUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class MeetingDetail extends Component
{
    public $current_meeting_slot;
    public $meeting_link;
    public $meeting_updates = [];

    #[On('copied-link-to-clipboard')]
    public function copy_meeting_link_to_clipboard()
    {
        Toaster::success('Meeting link copied to clipboard!');
    }

    public function update_meeting_details()
    {
        $this->validate(['meeting_link' => ['required', 'url']]);

        $this->current_meeting_slot->update(['meeting_link' => $this->meeting_link]);

        Toaster::success('Meeting details are now updated!');
        $this->dispatch('meeting-details-updated');
    }

    public function mount($meeting_uuid)
    {
        $this->current_meeting_slot = MeetingSlot::where('meeting_uuid', $meeting_uuid)->first();
        $this->meeting_link = $this->current_meeting_slot->meeting_link;

        $user = User::findOrFail(Auth::user()->id);

        if ($this->current_meeting_slot) {
            $this->meeting_updates[] = [
                'order' => 1,
                'headline' => 'Teacher opened the slot for ' .Helpers::parse_time_to_user_timezone($this->current_meeting_slot->start_time)->format('F j, Y g:i A'). ' ~ ' .Helpers::parse_time_to_user_timezone($this->current_meeting_slot->end_time)->format('g:i A'),
                'sub_text' => Helpers::parse_time_to_user_timezone($this->current_meeting_slot->created_at)->format('F j, Y g:i A'),
            ];
        }

        // Check if there are students who are already booked on this slot
        if ($this->current_meeting_slot->meeting_slots_users) {
            $students_in_meeting = $this->current_meeting_slot->meeting_slots_users
                ->map(fn ($student) =>
                    MeetingSlotsUser::where('meeting_slot_id', $this->current_meeting_slot->id)->isStudentId($student->id)
                        ->first()
                )->all();

            // Sort students who booked first in ascending order
            usort($students_in_meeting, fn ($a, $b) => $a['created_at'] <=> $b['created_at']);

            if (in_array($user->role->name, [Roles::HEAD_TEACHER->value, Roles::TEACHER->value])) {
                $this->meeting_updates[] = [
                    'order' => 2,
                    'headline' => 'Your meeting has been booked!',
                    'sub_text' => $students_in_meeting,
                ];
            } else if ($user->role->name == Roles::STUDENT->value) {
                $meeting_of_student = array_values(array_filter($students_in_meeting, fn ($user) => $user->student_id == Auth::user()->id));

                $this->meeting_updates[] = [
                    'order' => 2,
                    'headline' => 'You booked this slot!',
                    'sub_text' => Helpers::parse_time_to_user_timezone($meeting_of_student[0]->created_at)->format('F j, Y g:i A'),
                ];
            }
        }

        // Check if the meeting details were updated such as time was changed or date was changed
        // Goes here if there are students booked as it doesn't make sense to go here
        // If there are no booked students even if you update the meeting details
        if (array_key_exists('1', $this->meeting_updates) && $this->current_meeting_slot->meeting_link) {
            $this->meeting_updates[] = [
                'order' => 3,
                'headline' => 'Teacher updated meeting details',
                'sub_text' => Helpers::parse_time_to_user_timezone($this->current_meeting_slot->updated_at)->format('F j, Y g:i A'),
            ];
        }

        // Check if the meeting has reached some sort of conclusion
        if ($this->current_meeting_slot->status != MeetingStatuses::PENDING->value) {
            $this->meeting_updates[] = [
                'order' => 4,
                'headline' => 'Meeting resolved',
                'sub_text' => $this->current_meeting_slot->status,
            ];
        }
    }

    #[On('meeting-details-updated')]
    public function render()
    {
        $user = User::findOrFail(Auth::user()->id);

        if ($this->current_meeting_slot) {
            if ($user->cannot('view', $this->current_meeting_slot)) {
                abort(403);
            } else {
                return view('livewire.meetings.meeting-detail');
            }
        } else {
            abort(404);
        }
    }
}

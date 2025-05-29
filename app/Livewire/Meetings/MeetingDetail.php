<?php

namespace App\Livewire\Meetings;

use App\Enums\MeetingStatuses;
use App\Enums\Roles;
use App\Helpers\Helpers;
use App\Models\Meetings\Meeting;
use App\Models\Meetings\MeetingUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class MeetingDetail extends Component
{
    public string $cancel_reason = '';
    public $current_meeting;
    public bool $allow_meeting_link_edit = true;
    public bool $is_meeting_done = false;
    public $meeting_link;
    public $meeting_status;
    public $meeting_updates = [];
    public bool $show_cancel_meeting_modal = false;
    public $valid_statuses;

    #[On('copied-link-to-clipboard')]
    public function copy_meeting_link_to_clipboard()
    {
        Toaster::success('Meeting link copied to clipboard!');
    }

    public function is_current_time_less_than_end_time()
    {
        // Check to allow teachers to edit the meeting link before meeting ends
        $is_time_less_than_end_time = Helpers::parse_time_to_user_timezone(Carbon::now()) < Helpers::parse_time_to_user_timezone($this->current_meeting->end_time);

        return $is_time_less_than_end_time;
    }

    public function cancel_meeting_modal()
    {
        $this->show_cancel_meeting_modal = true;
    }

    public function cancel_meeting()
    {
        $this->validate(['cancel_reason' => ['required', 'min:5', 'max:255']]);

        $this->current_meeting->update([
            'notes' => $this->cancel_reason,
            'status' => MeetingStatuses::CANCELLED->value,
        ]);

        $this->cancel_reason = '';
        $this->show_cancel_meeting_modal = false;

        Toaster::success('Meeting has been cancelled');
        $this->dispatch('cancelled-meeting');
    }

    public function update_meeting_details()
    {
        $this->allow_meeting_link_edit = $this->is_current_time_less_than_end_time();

        // Validate based on which field is shown
        if ($this->allow_meeting_link_edit && !$this->is_meeting_done) {
            $this->validate(['meeting_link' => ['required', 'url']]);
        } else {
            $this->validate(['meeting_status' => ['required', Rule::in($this->valid_statuses)]]);
        }

        $fields_to_update = ['meeting_link' => $this->meeting_link];

        // TODO: OPTIONAL: WHEN RESOLVING A MEETING THAT CONCLUDED, SEND AN EMAIL TO THE RECIPIENTS TO NOTIFY THEM ABOUT IT
        if ($this->meeting_status != null) {
            $fields_to_update['status'] = $this->meeting_status;
        }

        $this->current_meeting->update($fields_to_update);

        Toaster::success('Meeting details are now updated!');
        $this->dispatch('updated-meeting');
    }

    public function mount($meeting_uuid)
    {
        $this->current_meeting = Meeting::where('meeting_uuid', $meeting_uuid)->first();
        $this->is_meeting_done = $this->current_meeting->status != MeetingStatuses::PENDING->value;
        $this->meeting_link = $this->current_meeting->meeting_link;
        $this->valid_statuses = collect(MeetingStatuses::cases())
            ->reject(fn ($case) => $case === MeetingStatuses::PENDING)
            ->map(fn ($case) => $case->value)
            ->all();

        $user = User::findOrFail(Auth::user()->id);

        if ($user->cannot('view', $this->current_meeting)) {
            abort(403);
        } else {
            if ($this->current_meeting) {
                $this->meeting_updates[] = [
                    'order' => 1,
                    'headline' => 'Teacher opened the slot for ' .Helpers::parse_time_to_user_timezone($this->current_meeting->start_time)->format('F j, Y g:i A'). ' ~ ' .Helpers::parse_time_to_user_timezone($this->current_meeting->end_time)->format('g:i A'),
                    'sub_text' => Helpers::parse_time_to_user_timezone($this->current_meeting->created_at)->format('F j, Y g:i A'),
                ];
            }

            // Check if there are students who are already booked on this slot
            if ($this->current_meeting->meeting_users) {
                $students_in_meeting = $this->current_meeting->meeting_users
                    ->map(fn ($student) => MeetingUser::where('meeting_id', $this->current_meeting->id)->isStudentId($student->id)
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
            if (array_key_exists('1', $this->meeting_updates) && $this->current_meeting->meeting_link) {
                $this->meeting_updates[] = [
                    'order' => 3,
                    'headline' => 'Teacher updated meeting details',
                    'sub_text' => Helpers::parse_time_to_user_timezone($this->current_meeting->updated_at)->format('F j, Y g:i A'),
                ];
            }

            // Check if the meeting has reached some sort of conclusion
            if ($this->is_meeting_done) {
                $this->meeting_updates[] = [
                    'order' => 4,
                    'headline' => 'Meeting resolved',
                    'sub_text' => $this->current_meeting->status,
                ];
            }
        }
    }

    #[On('cancelled-meeting')]
    #[On('updated-meeting')]
    public function render()
    {
        $this->allow_meeting_link_edit = $this->is_current_time_less_than_end_time();
        $this->is_meeting_done = $this->current_meeting->status != MeetingStatuses::PENDING->value;

        $user = User::findOrFail(Auth::user()->id);

        if ($this->current_meeting) {
            if ($user->cannot('view', $this->current_meeting)) {
                abort(403);
            } else {
                return view('livewire.meetings.meeting-detail');
            }
        } else {
            abort(404);
        }
    }
}

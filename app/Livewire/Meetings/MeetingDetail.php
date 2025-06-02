<?php

namespace App\Livewire\Meetings;

use App\Enums\MeetingStatuses;
use App\Helpers\Helpers;
use App\Models\Meetings\Meeting;
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
    public bool $is_student_role;
    public bool $is_teacher_role;
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

        if ($this->meeting_status != null) {
            $fields_to_update['status'] = $this->meeting_status;
        }

        $this->current_meeting->update($fields_to_update);

        if ($this->current_meeting->status == MeetingStatuses::PENDING->value) {
            $this->current_meeting->meeting_updates()->create([
                'user_id' => Auth::user()->id,
                'description' => 'Teacher updated meeting link',
            ]);
        } else {
            // TODO: OPTIONAL: WHEN RESOLVING A MEETING THAT CONCLUDED, SEND EMAIL TO THE RECIPIENTS TO NOTIFY THEM ABOUT IT
            $this->current_meeting->meeting_updates()->create([
                'user_id' => Auth::user()->id,
                'description' => 'Teacher updated meeting status',
            ]);
        }

        Toaster::success('Meeting details are now updated!');
        $this->dispatch('updated-meeting');
    }

    public function mount($meeting_uuid)
    {
        $this->current_meeting = Meeting::where('meeting_uuid', $meeting_uuid)->first();
        $this->is_meeting_done = $this->current_meeting->status != MeetingStatuses::PENDING->value;
        $this->is_student_role = Helpers::is_student_role();
        $this->is_teacher_role = Helpers::is_teacher_role();
        $this->meeting_link = $this->current_meeting->meeting_link;
        $this->valid_statuses = collect(MeetingStatuses::cases())
            ->reject(fn ($case) => $case === MeetingStatuses::PENDING)
            ->map(fn ($case) => $case->value)
            ->all();

        $user = User::findOrFail(Auth::user()->id);

        if ($user->cannot('view', $this->current_meeting)) {
            abort(403);
        } else {
            $this->current_meeting->meeting_updates->map(function ($update, $index) use ($user) {
                $values = [
                    'headline' => $update->description,
                    'sub_text' => Helpers::parse_time_to_user_timezone($update->created_at)->format('F j, Y h:i A'),
                    'time' => Helpers::parse_time_to_user_timezone($update->created_at)->format('F j, Y h:i A'),
                ];

                $user = User::find($update->user_id);

                if ($update->description == 'Teacher created meeting') {
                    $values['headline'] = 'Teacher created meeting for ' .Helpers::parse_time_to_user_timezone($update->old_meeting_time)->format('F j, Y h:i A'). ' ~ ' .Helpers::parse_time_to_user_timezone(Carbon::parse($update->old_meeting_time)->addMinutes(30))->format('h:i A');
                    $values['sub_text'] = Helpers::parse_time_to_user_timezone($this->current_meeting->created_at)->format('F j, Y h:i A');

                    array_push($this->meeting_updates, $values);
                } else if ($update->description == 'Student booked meeting') {
                    if ($this->is_student_role && ($this->current_meeting->teacher_id == $user->id || Auth::user()->id == $user->id)) {
                        $values['headline'] = 'You booked this meeting';
                        $values['sub_text'] = Helpers::parse_time_to_user_timezone($update->created_at)->format('F j, Y h:i A');
                        $values['user'] = $user;

                        array_push($this->meeting_updates, $values);
                    } else if ($this->is_teacher_role) {
                        $values['sub_text'] = 'booked on ' .Helpers::parse_time_to_user_timezone($update->created_at)->format('F j, Y h:i A');
                        $values['user'] = User::find($update->user_id);

                        array_push($this->meeting_updates, $values);
                    }
                } else if ($update->description == 'Teacher updated meeting link') {
                    $values['sub_text'] = Helpers::parse_time_to_user_timezone($update->created_at)->format('F j, Y h:i A');
                    $values['user'] = $user;

                    array_push($this->meeting_updates, $values);
                } else if ($update->description == 'Teacher updated meeting status') {
                    $values['sub_text'] = Helpers::parse_time_to_user_timezone($update->created_at)->format('F j, Y h:i A');

                    array_push($this->meeting_updates, $values);
                }

                return $values;
            })->all();

            // Sort by created_at field
            usort($this->meeting_updates, fn ($a, $b) => $a['time'] <=> $b['time']);
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

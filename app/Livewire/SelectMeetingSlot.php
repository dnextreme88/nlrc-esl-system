<?php

namespace App\Livewire;

use App\Enums\MeetingStatuses;
use App\Events\ReceiveMeetingBookedEvent;
use App\Helpers\Helpers;
use App\Mail\MeetingBookedEmail;
use App\Models\MeetingSlot;
use App\Models\MeetingSlotsUser;
use App\Models\User;
use App\Notifications\MeetingBookedNotification;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification as LaravelNotification;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class SelectMeetingSlot extends Component
{
    public array $possible_dates = [];
    public $available_meetings = [];
    public $meeting_date;
    public $show_reserve_slot_confirmation_modal;
    public $start_time;
    public $end_time;
    public $is_loading = false;
    public int $max_students_per_slot = 5;

    public function get_pending_slots($meeting_date): Collection
    {
        $available_meetings = MeetingSlot::where('status', MeetingStatuses::PENDING->value)->where('is_opened', 1)
            ->orderBy('start_time', 'ASC')
            ->get()
            ->filter(function ($meeting_slot) use ($meeting_date) {
                return Helpers::parse_time_to_user_timezone($meeting_slot['start_time'])->format('Y-m-d') == $meeting_date;
            });

        $meeting_slot_ids = $available_meetings->pluck('id');

        $booked_meeting_slots = MeetingSlotsUser::select(['meeting_slots_users.meeting_slot_id AS id', 'meeting_slots.start_time'])->whereIn('meeting_slot_id', $meeting_slot_ids)
            ->isStudentId(Auth::user()->id)
            ->join('meeting_slots', 'meeting_slots_users.meeting_slot_id', 'meeting_slots.id')
            ->get();

        // Check for duplicate start_time fields if more than 1 teacher has booked the same slot on the same meeting_date
        // Then remove any duplicates from the original collection since the student has already booked this start_time
        // And remove other duplicate start_times in case the student hasn't booked any slots
        $available_meetings = $available_meetings->reject(function ($slot) use ($booked_meeting_slots) {
            return $booked_meeting_slots->contains('start_time', $slot['start_time']) &&
                !$booked_meeting_slots->firstWhere('id', $slot['id']);
        })->unique('start_time');

        return $available_meetings;
    }

    public function reserve_slot_modal($start_time, $end_time)
    {
        $this->show_reserve_slot_confirmation_modal = true;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
    }

    public function reserve_slot()
    {
        // Assign to a random meeting slot if there are multiple teachers who has the same meeting date and times
        $random_meeting_slot = MeetingSlot::select(['id', 'teacher_id', 'start_time', 'end_time'])->where('meeting_date', Carbon::parse($this->start_time)->format('Y-m-d'))
            ->where('start_time', $this->start_time)
            ->where('end_time', $this->end_time)
            ->get()
            ->filter(fn ($val): bool => $val->students_count <= $this->max_students_per_slot);

        if ($random_meeting_slot->isNotEmpty()) {
            $random_meeting_slot = $random_meeting_slot->random();

            $random_meeting_slot->meeting_slots_users()->attach($random_meeting_slot->id, [
                'student_id' => Auth::user()->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $this->show_reserve_slot_confirmation_modal = false;
            $this->available_meetings = [];

            $meeting_teacher = User::find($random_meeting_slot->teacher_id);
            LaravelNotification::send(collect([Auth::user(), $meeting_teacher]), new MeetingBookedNotification($random_meeting_slot));

            // TODO: We can probably just send the email based on the users' setting preference
            Mail::to($meeting_teacher->email)->queue(new MeetingBookedEmail($random_meeting_slot, $meeting_teacher)); // Send to teacher
            Mail::to(Auth::user()->email)->queue(new MeetingBookedEmail($random_meeting_slot, Auth::user())); // Send to student

            broadcast(new ReceiveMeetingBookedEvent($meeting_teacher->id)); // Trigger an event
            broadcast(new ReceiveMeetingBookedEvent(Auth::user()->id)); // Trigger an event

            Toaster::success('You have successfully booked your slot!');
            $this->dispatch('reserved-slot');
        }
    }

    #[On('show-times-for-date')]
    public function show_available_times_for_selected_date($meeting_date)
    {
        $this->available_meetings = [];

        $this->meeting_date = Carbon::parse($meeting_date)->format('F j, Y');
        $pending_slots = $this->get_pending_slots($meeting_date);
        $this->available_meetings = $pending_slots->map(function ($meeting_slot_time) {
            $is_student_in_slot = $meeting_slot_time->meeting_slots_users->pluck('id')
                ->contains(fn ($user_id) => $user_id == Auth::user()->id);

            $meeting_slot_time = [
                'start_time' => $meeting_slot_time['start_time'],
                'end_time' => $meeting_slot_time['end_time'],
                'time' => Helpers::parse_time_to_user_timezone($meeting_slot_time['start_time'])->format('h:i A').  ' ~ ' .Helpers::parse_time_to_user_timezone($meeting_slot_time['end_time'])->format('h:i A'),
                'is_student_in_slot' => $is_student_in_slot,
            ];

            return $meeting_slot_time;
        });

        $this->is_loading = false;
    }

    public function mount()
    {
        $time_in_user_timezone = Carbon::now()->toUserTimezone();
        $time_in_user_timezone_tomorrow = $time_in_user_timezone->copy()->tomorrow();
        $next_28_days = $time_in_user_timezone->copy()->addDays(28);

        // Get all dates and determine which dates has pending meetings
        $all_dates = collect(CarbonPeriod::create($time_in_user_timezone_tomorrow, '1 day', $next_28_days))
            ->map(function ($carbon_instance) {
                $date = Carbon::parse($carbon_instance)->format('Y-m-d');
                $modified_available_meetings = $this->get_pending_slots($date);

                $this->possible_dates[$date] = $modified_available_meetings->map(fn ($meeting_slot_time) => [
                    'id' => $meeting_slot_time['id'],
                    'is_student_in_slot' => $meeting_slot_time->meeting_slots_users->pluck('id')
                        ->contains(fn ($user_id) => $user_id == Auth::user()->id),
                    'start_time' => Helpers::parse_time_to_user_timezone($meeting_slot_time['start_time'])->format('h:i A'),
                    'end_time' => Helpers::parse_time_to_user_timezone($meeting_slot_time['end_time'])->format('h:i A'),
                ]);
            });
    }

    #[On('reserved-slot')]
    public function render()
    {
        return view('livewire.select-meeting-slot');
    }
}

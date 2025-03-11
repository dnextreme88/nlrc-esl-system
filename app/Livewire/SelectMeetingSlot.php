<?php

namespace App\Livewire;

use App\Enums\MeetingStatuses;
use App\Models\MeetingSlot;
use App\Models\MeetingSlotUser;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class SelectMeetingSlot extends Component
{
    public array $possible_dates = [];
    public $available_meeting_slots_time = [];
    public $meeting_date;
    public bool $is_meeting_date_chosen = false;
    public $show_reserve_slot_confirmation_modal;
    public $start_time;
    public $end_time;
    public $time_in_user_timezone_tomorrow;
    public int $max_students_per_slot = 5;

    public function reserve_slot_modal($start_time, $end_time)
    {
        $this->show_reserve_slot_confirmation_modal = true;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
    }

    public function reserve_slot()
    {
        // Assign to a random meeting slot if there are multiple teachers who has the same meeting date and times
        $random_meeting_slot = MeetingSlot::select(['id', 'start_time', 'end_time'])->where('meeting_date', $this->meeting_date)
            ->where('start_time', $this->start_time)
            ->where('end_time', $this->end_time)
            ->get()
            ->filter(fn ($val): bool => $val->students_count <= $this->max_students_per_slot);

        if ($random_meeting_slot->isNotEmpty()) {
            $random_meeting_slot = $random_meeting_slot->random();

            $random_meeting_slot->meeting_slot_users()->attach($random_meeting_slot->id, [
                'student_id' => Auth::user()->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $this->show_reserve_slot_confirmation_modal = false;
            $this->meeting_date = null;
            $this->is_meeting_date_chosen = false;

            Toaster::success('You have successfully booked your slot!');
            $this->dispatch('reserved-slot');
        }
    }

    public function show_available_times_for_selected_date()
    {
        $this->validate(['meeting_date' => ['required', 'date', 'date_format:Y-m-d']]);

        $meeting_date = $this->meeting_date;

        // TODO: TO REFACTOR LOGIC TO LIMIT 5 STUDENTS PER MEETING SLOT PER UNIQUE TEACHER
        $this->available_meeting_slots_time = [];
        $available_meeting_slots_time = MeetingSlot::where('status', MeetingStatuses::PENDING->value)->where('is_opened', 1)
            ->orderBy('start_time', 'ASC')
            ->get()
            ->filter(function ($meeting_slot) use ($meeting_date) {
                return Carbon::parse($meeting_slot['start_time'])->toUserTimezone()->format('Y-m-d') == $meeting_date;
            });

        $meeting_slot_ids = $available_meeting_slots_time->pluck('id');

        $booked_meeting_slots = MeetingSlotUser::select(['meeting_slot_users.meeting_slot_id AS id', 'meeting_slots.start_time'])->whereIn('meeting_slot_id', $meeting_slot_ids)->where('student_id', Auth::user()->id)
            ->join('meeting_slots', 'meeting_slot_users.meeting_slot_id', 'meeting_slots.id')
            ->get();

        $modified_available_meeting_slots_time = $available_meeting_slots_time;

        foreach ($available_meeting_slots_time as $available_meeting_slot) {
            // Check for duplicate start_time fields that usually happens if more than 1 teacher has booked the same slot
            // on the same meeting_date
            if ($booked_meeting_slots->contains('start_time', $available_meeting_slot['start_time'])) {
                $meeting_slot_to_be_removed = $booked_meeting_slots->firstWhere('id', $available_meeting_slot['id']);

                // Remove any duplicates from the original collection since the student has already booked this start_time
                if (!$meeting_slot_to_be_removed) {
                    $modified_available_meeting_slots_time = $modified_available_meeting_slots_time->reject(function ($slot) use ($available_meeting_slot) {
                        return $slot['id'] == $available_meeting_slot['id'];
                    });
                }
            }
        }

        // Remove any other duplicate start_times just in case the student hasn't booked any slots for a particular meeting_date
        $this->available_meeting_slots_time = $modified_available_meeting_slots_time->unique('start_time');

        $this->is_meeting_date_chosen = true;
    }

    public function mount()
    {
        $time_in_user_timezone = Carbon::now()->toUserTimezone();
        $this->time_in_user_timezone_tomorrow = $time_in_user_timezone->copy()->tomorrow();
        $next_28_days = $time_in_user_timezone->copy()->addDays(28);

        // Get the dates starting tomorrow
        $period = CarbonPeriod::create($this->time_in_user_timezone_tomorrow, '1 day', $next_28_days);

        foreach ($period as $date) {
            $this->possible_dates[] = $date;
        }
    }

    #[On('reserved-slot')]
    public function render()
    {
        return view('livewire.select-meeting-slot');
    }
}

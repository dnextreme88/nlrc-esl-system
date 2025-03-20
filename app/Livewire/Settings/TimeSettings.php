<?php

namespace App\Livewire\Settings;

use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use DateTimeZone;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class TimeSettings extends Component
{
    public $current_time;
    public $timezone;
    public array $timezones_list;
    public $user;

    // Lifecycle hook
    public function updatedTimezone()
    {
        $this->update_current_time();
    }

    public function update_current_time()
    {
        $this->current_time = $this->timezone ? 'It should now be ' .Carbon::now($this->timezone)->format('H:i A'). ' in your area. Select a different timezone if that is not the case.' : '';
    }

    public function update_timezone_settings()
    {
        $this->validate(['timezone' => ['required']]);

        $this->user->update(['timezone' => $this->timezone]);

        Toaster::success('Your timezone is now updated!');
        $this->dispatch('timezone-settings-updated');
    }

    public function mount()
    {
        $this->user = Auth::user();
        $this->timezone = $this->user->timezone;

        $timezone_names = DateTimeZone::listIdentifiers(timezoneGroup: DateTimeZone::ALL);
        $this->timezones_list = array_combine(
            $timezone_names,
            array_map(fn ($tz) => (new CarbonTimeZone($tz))->toOffsetName(), $timezone_names)
        );

        $this->update_current_time();
    }

    #[On('timezone-settings-updated')]
    public function render()
    {
        return view('livewire.settings.time-settings');
    }
}

<?php

namespace App\Livewire\Settings;

use Livewire\Component;

class UserSettings extends Component
{
    public function render()
    {
        return view('livewire.settings.user-settings')->layout('components.layouts.settings');
    }
}

<?php

namespace App\Livewire\Settings;

use Livewire\Component;

class SecuritySettings extends Component
{
    public function render()
    {
        return view('livewire.settings.security-settings')->layout('components.layouts.settings');
    }
}

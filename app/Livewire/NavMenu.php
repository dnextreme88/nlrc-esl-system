<?php

namespace App\Livewire;

use Livewire\Component;

// Using NavigationMenu is conflicting with another class of the same name from Laravel Jetstream
class NavMenu extends Component
{
    public function render()
    {
        return view('livewire.nav-menu');
    }
}

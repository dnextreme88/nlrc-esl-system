<?php

namespace App\Livewire\Modules\Units;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UnitDetail extends Component
{
    public $current_unit;
    public $module_id;
    public $module_slug;

    public function mount($id, $slug, $unit_id, $unit_slug)
    {
        $this->module_id = $id;
        $this->module_slug = $slug;

        $this->current_unit = Unit::where('id', $unit_id)->where('slug', $unit_slug)
            ->whereHas('module', fn ($query) => $query->where('id', $id)->where('slug', $slug))
            ->first();
    }

    public function render()
    {
        $user = User::findOrFail(Auth::user()->id);

        if ($this->current_unit) {
            if ($user->cannot('view', $this->current_unit->module)) {
                abort(403);
            } else {
                return view('livewire.modules.units.unit-detail');
            }
        } else {
            abort(404);
        }
    }
}

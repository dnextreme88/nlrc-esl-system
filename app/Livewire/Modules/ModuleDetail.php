<?php

namespace App\Livewire\Modules;

use App\Models\Module;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ModuleDetail extends Component
{
    use WithPagination;

    public $current_module;

    public function mount($id, $slug)
    {
        $this->current_module = Module::where('id', $id)->where('slug', $slug)
            ->first();
    }

    public function render()
    {
        $user = User::findOrFail(Auth::user()->id);

        if ($this->current_module) {
            if ($user->cannot('view', $this->current_module)) {
                abort(403);
            } else {
                return view('livewire.modules.module-detail');
            }
        } else {
            abort(404);
        }
    }
}

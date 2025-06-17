<?php

namespace App\Livewire;

use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

class ProjectLogs extends Component
{
    public $logs;

    public function mount()
    {
        $query = Activity::query();
        if (session("roleId") != 1) {
            $query->company_id = session('company_id');
        }
        $this->logs = $query->get();
    }

    public function render()
    {
        return view('livewire.project-logs');
    }
}

<?php

namespace App\Livewire\Sales;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class PremiumTerminalDetails extends Component
{
    public $terminalId;
    public $openingId;
    public $heads = [];
    public $result = [];
    public $terminal_name = [];

    public function mount($terminal, $opening)
    {
        $this->terminalId = $terminal;
        $this->openingId = $opening;
        $this->loadData();
    }

    public function loadData()
    {
        $dash = new \App\dashboard();
        $user = new \App\userDetails();
        
        $this->result = $user->getPermission($this->terminalId);
        $this->terminal_name = $user->getTerminalName($this->terminalId);
        $this->heads = $dash->headsDetails($this->terminalId);
        
        if (empty($this->heads)) {
            $this->heads = $dash->lastDayDetails($this->terminalId);
        }
    }

    public function render()
    {
        return view('livewire.sales.premium-terminal-details');
    }
}

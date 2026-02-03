<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BookingSlots extends Component
{
    public $slots = [];
    public $mode = '';
    public $type = '';
    public $start_time = '';
    public $end_time = '';
    public $editId = null;

    public function mount()
    {
        $this->loadSlots();
    }

    public function loadSlots()
    {
        $this->slots = DB::table('booking_slots')
            ->where('company_id', session('company_id'))
            ->where('status', 1)
            ->orderBy('mode')
            ->orderBy('type')
            ->orderBy('start_time')
            ->get();
    }

    public function edit($id)
    {
        $slot = DB::table('booking_slots')->where('id', $id)->first();
        if ($slot) {
            $this->editId = $slot->id;
            $this->mode = $slot->mode;
            $this->type = $slot->type;
            $this->start_time = $slot->start_time;
            $this->end_time = $slot->end_time;
        }
    }

    public function save()
    {
        $this->validate([
            'mode' => 'required',
            'type' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        if ($this->editId) {
            DB::table('booking_slots')
                ->where('id', $this->editId)
                ->update([
                    'mode' => $this->mode,
                    'type' => $this->type,
                    'start_time' => $this->start_time,
                    'end_time' => $this->end_time,
                ]);
        } else {
            DB::table('booking_slots')->insert([
                'company_id' => session('company_id'),
                'mode' => $this->mode,
                'type' => $this->type,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'status' => 1,
                'created_at' => now(),
            ]);
        }

        $this->reset(['mode', 'type', 'start_time', 'end_time', 'editId']);
        $this->loadSlots();
        session()->flash('success', 'Slot saved successfully!');
    }

    public function delete($id)
    {
        DB::table('booking_slots')->where('id', $id)->update(['status' => 0]);
        $this->loadSlots();
        session()->flash('success', 'Slot deleted successfully!');
    }

    public function render()
    {
        return view('livewire.booking-slots');
    }
}

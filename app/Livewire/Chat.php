<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Chat as ChatModel;
use Illuminate\Support\Facades\Auth;

class Chat extends Component
{
    public $message = '';
    public $messages = [];
    public $loading = false;

    public function mount()
    {
        $this->getMessages();
    }

    public function getMessages()
    {
        $this->messages = ChatModel::orderBy('created_at')->get();
    }

    public function sendMessage()
    {
        $this->validate([
            'message' => 'required|string',
        ]);
        $this->loading = true;
        ChatModel::create([
            'sender_id' => Auth::id(),
            'receiver_id' => null,
            'message' => $this->message,
            'is_read' => false,
        ]);
        $this->message = '';
        $this->loading = false;
        $this->getMessages();
    }

    public function render()
    {
        return view('livewire.chat');
    }
}

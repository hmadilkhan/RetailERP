<?php

namespace App\Livewire;

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Services\NL2SQLService;
use Livewire\Component;

class ChatComponent extends Component
{
    public $currentChatId = null;
    public $messageContent = '';
    public $currentChat = null;
    public $isProcessing = false;

    public function mount()
    {
        $chatId = request()->query('chat');
        if ($chatId) {
            $this->selectChat($chatId);
        }
    }

    public function selectChat($chatId)
    {
        $this->currentChatId = $chatId;
        $this->currentChat = Chat::with(['messages' => function($q) {
            $q->orderBy('created_at')->take(50);
        }])->find($chatId);
        
        $this->dispatch('scrollToBottom');
    }

    public function createNewChat()
    {
        $chat = Chat::create(['title' => null]);
        $this->selectChat($chat->id);
        $this->dispatch('urlChanged', ['url' => route('chat.index', ['chat' => $chat->id])]);
    }

    public function sendMessage()
    {
        if (empty(trim($this->messageContent))) {
            return;
        }

        if (!$this->currentChat) {
            return;
        }

        $this->isProcessing = true;

        // Create user message
        $userMsg = new ChatMessage([
            'role' => 'user',
            'content' => $this->messageContent,
        ]);
        $this->currentChat->messages()->save($userMsg);

        // Update chat title if needed
        if (!$this->currentChat->title) {
            $this->currentChat->title = mb_substr($this->messageContent, 0, 80);
            $this->currentChat->save();
        }

        // Create assistant message placeholder
        $assistant = new ChatMessage([
            'role' => 'assistant',
            'content' => 'Thinking…',
        ]);
        $this->currentChat->messages()->save($assistant);

        // Process with NL2SQL service
        try {
            $service = app(NL2SQLService::class);
            $result = $service->handleUserMessage($this->currentChat, $assistant);

            if (!$result['ok']) {
                $assistant->content = 'Error: ' . $result['error'];
                $assistant->save();
            } else {
                $assistant->content = 'OK';
                $assistant->save();
            }
        } catch (\Exception $e) {
            $assistant->content = 'Error: ' . $e->getMessage();
            $assistant->save();
        }

        // Clear input and reload chat
        $this->messageContent = '';
        $this->isProcessing = false;
        
        // Reload chat with messages to reflect updated result/sql
        $this->selectChat($this->currentChatId);
        
        $this->dispatch('scrollToBottom');
    }

    public function render()
    {
        $chats = Chat::orderByDesc('created_at')->take(50)->get();
        
        return view('livewire.chat-component', [
            'chats' => $chats
        ]);
    }
} 
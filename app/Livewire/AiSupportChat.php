<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Http;
use Livewire\Component;

class AiSupportChat extends Component
{
    public $messages = [];
    public $userInput = '';

    public function sendMessage()
    {
        $userMessage = $this->userInput;
        $this->messages[] = ['sender' => 'user', 'text' => $userMessage];
        $this->userInput = '';

        $response = Http::withToken(env('OPENAI_API_KEY'))->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => 'You are an ERP assistant that helps with purchase, sales, inventory, and support issues.'],
                ['role' => 'user', 'content' => $userMessage],
            ],  
        ]);

        $aiReply = $response['choices'][0]['message']['content'] ?? 'Sorry, no response from AI.';
        $this->messages[] = ['sender' => 'ai', 'text' => $aiReply];
    }


    public function render()
    {
        return view('livewire.ai-support-chat');
    }
}

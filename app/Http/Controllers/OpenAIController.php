<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OpenAIController extends Controller
{
    public function generateStory(Request $request)
    {
        $response = Http::withToken(env('OPENAI_API_KEY'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4',
                'messages' => [
                    ['role' => 'user', 'content' => 'Write a one-sentence bedtime story about a unicorn.']
                ],
                'temperature' => 0.7,
            ]);

        return response()->json($response->json());
    }
}

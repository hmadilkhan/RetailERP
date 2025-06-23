<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OpenAIController extends Controller
{
    public function generateStory(Request $request)
    {
        $prompt = "Generate brief description of product " . $request->name . " of maximum 50 words with Search Engine Optimization.";

        $headers = [
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type' => 'application/json',
        ];

        $body = [
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.7,
            'stream' => true,
        ];

        $response = new StreamedResponse(function () use ($headers, $body) {
            $client = curl_init('https://api.openai.com/v1/chat/completions');
            curl_setopt_array($client, [
                CURLOPT_RETURNTRANSFER => false,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . env('OPENAI_API_KEY'),
                    'Content-Type: application/json',
                ],
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($body),
                CURLOPT_WRITEFUNCTION => function ($ch, $data) {
                    echo $data;
                    ob_flush();
                    flush();
                    return strlen($data);
                }
            ]);

            curl_exec($client);
            curl_close($client);
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('X-Accel-Buffering', 'no'); // for nginx
        return $response;
    }

    function generateProductImage(Request $request)
    {
        $imagePrompt = "Create a minimalist product ad image for " . $request->name;

        // Step 2: Generate image using DALL·E 3
        $imageResponse = Http::withToken(env('OPENAI_API_KEY'))->post('https://api.openai.com/v1/images/generations', [
            'model' => 'dall-e-3',
            'prompt' => $imagePrompt . " with text overlay: '" . $request->name . "'",
            'size' => '100x100',
            'n' => 1,
            'response_format' => 'url',
        ]);

        $imageUrl = $imageResponse['data'][0]['url'] ?? null;

        return response()->json([
            'image_url' => $imageUrl,
        ]);
    }
}

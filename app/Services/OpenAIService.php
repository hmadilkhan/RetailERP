<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
	public function generateSql(string $prompt): string
	{
		$apiKey = config('services.openai.key');
		
		// Debug: Log the API key (remove this in production)
		if (empty($apiKey)) {
			$apiKey = env('OPENAI_API_KEY');
			Log::warning('OpenAI API key not found in config, using env() directly');
		}
		
		if (empty($apiKey)) {
			throw new \Exception('OpenAI API key not configured. Please check your .env file.');
		}
		
		$model = config('services.openai.model', 'gpt-4o-mini');
		$client = new Client([
			'base_uri' => 'https://api.openai.com/v1/',
			'timeout' => 30,
		]);

		$response = $client->post('chat/completions', [
			'headers' => [
				'Authorization' => 'Bearer ' . $apiKey,
				'Content-Type' => 'application/json',
			],
			'json' => [
				'model' => $model,
				'messages' => [
					['role' => 'system', 'content' => 'You are a SQL generator. Output only a single SQL statement without backticks or markdown. Only read-only queries allowed.'],
					['role' => 'user', 'content' => $prompt],
				],
				'temperature' => 0,
				'max_tokens' => 1000,
			],
		]);

		$body = json_decode((string)$response->getBody(), true);
		$text = $body['choices'][0]['message']['content'] ?? '';
		// strip code fences and markdown
		$text = trim($text);
		$text = preg_replace('/^```[a-zA-Z]*\n|```$/m', '', $text);
		return trim($text);
	}
}

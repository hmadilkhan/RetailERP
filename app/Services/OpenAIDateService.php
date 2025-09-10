<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenAIDateService
{
    public function parseDateWindow(string $userText, \DateTimeInterface $now, string $tz = 'Asia/Karachi'): array
    {
        $system = <<<SYS
You are a date normalizer. Return ONLY a compact JSON object:
{
  "type": "specific" | "range" | "none",
  "start": "YYYY-MM-DD" | null,
  "end": "YYYY-MM-DD" | null,
  "needs_clarification": true|false,
  "reason": "string (optional when needs_clarification=true)"
}

Rules:
- Timezone: {$tz}. Current date/time: {$now->format('Y-m-d H:i:s')} ({$tz}).
- Understand English and Roman Urdu (e.g., "aaj"=today, "kal"=yesterday, "pichlay 15 din"=last 15 days, "is hafta"=this week, "pichlay haftay"=last week, "is maheena"=this month, "pichlay maheene"=last month; "din"=days, "hafta"=week, "maheena"=month).
- "today" => start=end=today.
- "yesterday"/"kal" => that single day (start=end).
- Ranges are inclusive calendar days.
- If only a month name is given, pick the most sensible recent year.
- If ambiguous or invalid (e.g., "31 Sept"), set needs_clarification=true and type="none".
- Output must be VALID JSON. No extra text.
SYS;

        $resp = Http::withToken(env('OPENAI_API_KEY'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini', // pick your deployed model
                'temperature' => 0,
                'messages' => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => "Text: {$userText}"],
                ],
            ])->json();
        
        $raw = $resp['choices'][0]['message']['content'] ?? '{}';
        $out = json_decode($raw, true);
        
        if (!is_array($out)) {
            $out = [
                'type' => 'none',
                'start' => null,
                'end' => null,
                'needs_clarification' => true,
                'reason' => 'Non-JSON response',
            ];
        }

        // Guards
        $out += ['type' => 'none', 'start' => null, 'end' => null, 'needs_clarification' => false];

        return $out;
    }

    // You likely already have this:
    public function ask(array $messages): string
    {
        $resp = Http::withToken(env('OPENAI_API_KEY'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o', // or your choice
                'temperature' => 0.2,
                'messages' => $messages,
            ])->json();

        return $resp['choices'][0]['message']['content'] ?? 'No response.';
    }
}

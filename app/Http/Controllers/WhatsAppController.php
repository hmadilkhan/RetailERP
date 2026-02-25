<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    private $token;
    private $phoneId;
    private $verifyToken;

    public function __construct()
    {
        $this->token = config('services.whatsapp.token');
        $this->phoneId = config('services.whatsapp.phone_id');
        $this->verifyToken = config('services.whatsapp.verify_token');
    }

    /*
    |--------------------------------------------------------------------------
    | 1️⃣ Webhook Verification (Meta Setup ke liye)
    |--------------------------------------------------------------------------
    */
    public function verify(Request $request)
    {
        $verifyToken = $request->query('hub.verify_token', $request->query('hub_verify_token'));
        $challenge = $request->query('hub.challenge', $request->query('hub_challenge'));

        if ($verifyToken === $this->verifyToken) {
            Log::info($request->all());
            return response($challenge, 200);
        }

        return response("Invalid token", 403);
    }

    /*
    |--------------------------------------------------------------------------
    | 2️⃣ Handle Incoming Messages
    |--------------------------------------------------------------------------
    */
    public function handle(Request $request)
    {
        Log::info($request->all());

        $message = $request->entry[0]['changes'][0]['value']['messages'][0] ?? null;

        if (!$message) {
            return response()->json(['status' => 'no message']);
        }

        $from = $message['from'];

        // TEXT MESSAGE
        if (isset($message['text'])) {
            $text = strtolower(trim($message['text']['body']));

            if ($text == 'hi' || $text == 'hello') {
                $this->sendMenu($from);
            }
        }

        // BUTTON RESPONSE
        if (isset($message['interactive']['button_reply'])) {
            $buttonId = $message['interactive']['button_reply']['id'];

            if ($buttonId == "track_order") {
                $this->sendText($from, "Please enter your Order Number.");
            }

            if ($buttonId == "monthly_report") {
                $this->sendText($from, "Please enter your registered mobile number to get report.");
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /*
    |--------------------------------------------------------------------------
    | 3️⃣ Send Interactive Menu
    |--------------------------------------------------------------------------
    */
    private function sendMenu($number)
    {
        $payload = [
            "messaging_product" => "whatsapp",
            "to" => $number,
            "type" => "interactive",
            "interactive" => [
                "type" => "button",
                "body" => [
                    "text" => "Welcome to Sabsons Distribution 👋\nPlease choose an option:"
                ],
                "action" => [
                    "buttons" => [
                        [
                            "type" => "reply",
                            "reply" => [
                                "id" => "track_order",
                                "title" => "📦 Track Order"
                            ]
                        ],
                        [
                            "type" => "reply",
                            "reply" => [
                                "id" => "monthly_report",
                                "title" => "📊 Monthly Report"
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $this->sendRequest($payload);
    }

    /*
    |--------------------------------------------------------------------------
    | 4️⃣ Send Simple Text Message
    |--------------------------------------------------------------------------
    */
    private function sendText($number, $message)
    {
        $payload = [
            "messaging_product" => "whatsapp",
            "to" => $number,
            "type" => "text",
            "text" => [
                "body" => $message
            ]
        ];

        $this->sendRequest($payload);
    }

    /*
    |--------------------------------------------------------------------------
    | 5️⃣ Send API Request
    |--------------------------------------------------------------------------
    */
    private function sendRequest($payload)
    {
        Http::withToken($this->token)
            ->post("https://graph.facebook.com/v17.0/{$this->phoneId}/messages", $payload);
    }
}

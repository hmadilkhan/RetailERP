<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWhatsappReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phone;
    protected $fileUrl;
    protected $customerName;
    protected $month;
    protected $fileName;

    public function __construct($phone, $fileUrl, $customerName, $month, $fileName)
    {
        $this->phone = $phone;
        $this->fileUrl = $fileUrl;
        $this->customerName = $customerName;
        $this->month = $month;
        $this->fileName = $fileName;
    }

    public function handle()
    {
        try {

            $token = env('WHATSAPP_TOKEN');
            $phoneNumberId = env('WHATSAPP_PHONE_NUMBER_ID');

            Log::info('Token:', ['token' => env('WHATSAPP_TOKEN')]);
            Log::info('Phone ID:', ['id' => env('WHATSAPP_PHONE_NUMBER_ID')]);

            $response = Http::withToken($token)->post(
                "https://graph.facebook.com/v18.0/$phoneNumberId/messages",
                [
                    "messaging_product" => "whatsapp",
                    "to" => $this->phone,
                    "type" => "template",
                    "template" => [
                        "name" => "report_document",
                        "language" => [
                            "code" => "en"
                        ],
                        "components" => [
                            [
                                "type" => "header",
                                "parameters" => [
                                    [
                                        "type" => "document",
                                        "document" => [
                                            "link" => $this->fileUrl,
                                            "filename" => $this->fileName
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "type" => "body",
                                "parameters" => [
                                    [
                                        "type" => "text",
                                        "text" => $this->customerName
                                    ],
                                    [
                                        "type" => "text",
                                        "text" => "FBR"
                                    ],
                                    [
                                        "type" => "text",
                                        "text" => $this->month
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            );
            Log::info('WhatsApp Report Response', [
                'response' => $response->json()
            ]);
        } catch (\Exception $e) {

            Log::error('WhatsApp Report Failed', [
                'error' => $e->getMessage()
            ]);
        }
    }
}

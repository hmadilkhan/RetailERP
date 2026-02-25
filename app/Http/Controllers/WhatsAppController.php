<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    private const STATE_TTL_SECONDS = 1800;

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
        $state = $this->getConversationState($from);

        // BUTTON RESPONSE
        if (isset($message['interactive']['button_reply'])) {
            $buttonId = $message['interactive']['button_reply']['id'];

            if ($buttonId == "track_order") {
                $this->sendText($from, "Please enter your Order Number.");
            }

            if ($buttonId == "monthly_report") {
                $this->setConversationState($from, ['step' => 'awaiting_report_type']);
                $this->sendMonthlyReportMenu($from);
            }

            if ($buttonId == "fbr_report" || $buttonId == "sales_report") {
                $this->setConversationState($from, [
                    'step' => 'awaiting_mobile',
                    'report_type' => $buttonId,
                ]);
                $this->sendText($from, "Please enter your registered mobile number.");
            }
        }

        // TEXT MESSAGE
        if (isset($message['text'])) {
            $textRaw = trim($message['text']['body']);
            $text = strtolower($textRaw);

            if (($state['step'] ?? null) === 'awaiting_mobile') {
                $mobile = $this->normalizeMobile($textRaw);

                if (!$mobile) {
                    $this->sendText($from, "Invalid mobile number. Please enter a valid registered mobile number.");
                    return response()->json(['status' => 'ok']);
                }

                $state['mobile'] = $mobile;
                $state['step'] = 'awaiting_terminal';
                $this->setConversationState($from, $state);
                $this->sendText($from, "Please enter terminal number.");
                return response()->json(['status' => 'ok']);
            }

            if (($state['step'] ?? null) === 'awaiting_terminal') {
                $terminal = trim($textRaw);

                if ($terminal === '') {
                    $this->sendText($from, "Terminal number is required. Please enter terminal number.");
                    return response()->json(['status' => 'ok']);
                }

                $state['terminal'] = $terminal;
                $state['step'] = 'awaiting_from_date';
                $this->setConversationState($from, $state);
                $this->sendText($from, "Please enter from date in YYYY-MM-DD format.");
                return response()->json(['status' => 'ok']);
            }

            if (($state['step'] ?? null) === 'awaiting_from_date') {
                $fromDate = $this->normalizeDate($textRaw);

                if (!$fromDate) {
                    $this->sendText($from, "Invalid from date. Please use YYYY-MM-DD format.");
                    return response()->json(['status' => 'ok']);
                }

                $state['from_date'] = $fromDate;
                $state['step'] = 'awaiting_to_date';
                $this->setConversationState($from, $state);
                $this->sendText($from, "Please enter to date in YYYY-MM-DD format.");
                return response()->json(['status' => 'ok']);
            }

            if (($state['step'] ?? null) === 'awaiting_to_date') {
                $toDate = $this->normalizeDate($textRaw);

                if (!$toDate) {
                    $this->sendText($from, "Invalid to date. Please use YYYY-MM-DD format.");
                    return response()->json(['status' => 'ok']);
                }

                if ($toDate < ($state['from_date'] ?? '')) {
                    $this->sendText($from, "To date cannot be earlier than from date. Please enter a valid to date.");
                    return response()->json(['status' => 'ok']);
                }

                $state['to_date'] = $toDate;
                $this->processReportRequest($from, $state);
                $this->clearConversationState($from);
                return response()->json(['status' => 'ok']);
            }

            if ($text == 'hi' || $text == 'hello') {
                $this->clearConversationState($from);
                $this->sendMenu($from);
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

    private function sendMonthlyReportMenu($number)
    {
        $payload = [
            "messaging_product" => "whatsapp",
            "to" => $number,
            "type" => "interactive",
            "interactive" => [
                "type" => "button",
                "body" => [
                    "text" => "Please choose report type:"
                ],
                "action" => [
                    "buttons" => [
                        [
                            "type" => "reply",
                            "reply" => [
                                "id" => "fbr_report",
                                "title" => "FBR Report"
                            ]
                        ],
                        [
                            "type" => "reply",
                            "reply" => [
                                "id" => "sales_report",
                                "title" => "Sales Report"
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

    private function processReportRequest($to, array $state)
    {
        $reportType = $state['report_type'] ?? null;
        $mobile = $state['mobile'] ?? null;
        $terminal = $state['terminal'] ?? null;
        $fromDate = $state['from_date'] ?? null;
        $toDate = $state['to_date'] ?? null;

        if (!$reportType || !$mobile || !$terminal || !$fromDate || !$toDate) {
            $this->sendText($to, "Missing required details. Please type Hi and try again.");
            return;
        }

        $this->sendText($to, "Please wait while we prepare your report.");

        // TODO: Replace this stub with your actual report generation function.
        // Expected return:
        // [
        //   'url' => 'https://your-domain.com/storage/reports/abc.pdf',
        //   'filename' => 'report.pdf'
        // ]
        $report = $this->buildReportAndGetPdf($reportType, $mobile, $terminal, $fromDate, $toDate);

        if (!$report || empty($report['url'])) {
            $this->sendText($to, "Could not find report PDF. Please verify dates/terminal and try again.");
            return;
        }

        $templateName = 'report';
        $language = 'en';
        $reportName = $reportType === 'fbr_report' ? 'FBR Report' : 'Sales Report';
        $filename = $report['filename'] ?? ($reportName . '.pdf');

        $this->sendTemplateWithDocument(
            $to,
            $templateName,
            $language,
            $report['url'],
            $filename,
            $reportName,
            $mobile,
            $terminal
        );
    }

    private function buildReportAndGetPdf($reportType, $mobile, $terminal, $fromDate, $toDate)
    {
        Log::info('Report request captured', [
            'report_type' => $reportType,
            'mobile' => $mobile,
            'terminal' => $terminal,
            'from_date' => $fromDate,
            'to_date' => $toDate,
        ]);

        if ($reportType !== 'fbr_report') {
            return null;
        }

        $terminalInfo = DB::table('terminal_details')
            ->join('branch', 'branch.branch_id', '=', 'terminal_details.branch_id')
            ->join('company', 'company.company_id', '=', 'branch.company_id')
            ->select('terminal_details.terminal_id', 'company.name as company_name')
            ->where('terminal_details.terminal_id', $terminal)
            ->first();

        if (!$terminalInfo) {
            return null;
        }

        $fileName = 'FBR_REPORT_' . date('M', strtotime($fromDate)) . '_' . $terminalInfo->company_name . '.pdf';
        $filePath = storage_path('app/public/pdfs/' . $fileName);

        if (!file_exists($filePath)) {
            return null;
        }

        return [
            'url' => 'https://retail.sabsoft.com.pk/storage/pdfs/' . rawurlencode($fileName),
            'filename' => $fileName,
        ];
    }

    private function sendTemplateWithDocument($to, $templateName, $language, $documentUrl, $filename, $reportName, $mobile, $terminal)
    {
        if (!$templateName) {
            $this->sendText($to, "Template is not configured. Please contact support.");
            return;
        }

        $payload = [
            "messaging_product" => "whatsapp",
            "to" => $to,
            "type" => "template",
            "template" => [
                "name" => $templateName,
                "language" => [
                    "code" => $language
                ],
                "components" => [
                    [
                        "type" => "header",
                        "parameters" => [
                            [
                                "type" => "document",
                                "document" => [
                                    "link" => $documentUrl,
                                    "filename" => $filename
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "body",
                        "parameters" => [
                            ["type" => "text", "text" => $reportName],
                            ["type" => "text", "text" => $mobile],
                            ["type" => "text", "text" => $terminal]
                        ]
                    ]
                ]
            ]
        ];

        $this->sendRequest($payload);
    }

    private function normalizeMobile($value)
    {
        $digits = preg_replace('/\D+/', '', $value);
        return strlen($digits) >= 10 ? $digits : null;
    }

    private function normalizeDate($value)
    {
        try {
            return Carbon::createFromFormat('Y-m-d', trim($value))->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getConversationState($number)
    {
        return Cache::get($this->stateKey($number), []);
    }

    private function setConversationState($number, array $state)
    {
        Cache::put($this->stateKey($number), $state, now()->addSeconds(self::STATE_TTL_SECONDS));
    }

    private function clearConversationState($number)
    {
        Cache::forget($this->stateKey($number));
    }

    private function stateKey($number)
    {
        return "wa_state:{$number}";
    }
}

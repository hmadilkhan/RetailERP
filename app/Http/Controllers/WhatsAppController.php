<?php

namespace App\Http\Controllers;

use App\pdfClass;
use App\report as ReportModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    private const STATE_TTL_SECONDS = 1800;
    private const BRANCH_PAGE_SIZE = 8;
    private const TERMINAL_PAGE_SIZE = 8;

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

        $messageId = $message['id'] ?? null;
        if ($messageId && !$this->acquireInboundMessageLock($messageId)) {
            Log::info('Duplicate WhatsApp webhook message ignored (in-progress lock)', ['message_id' => $messageId]);
            return response()->json(['status' => 'ok']);
        }

        $from = $message['from'];
        $state = $this->getConversationState($from);

        // LIST RESPONSE
        if (isset($message['interactive']['list_reply'])) {
            $listId = $message['interactive']['list_reply']['id'] ?? '';

            if (($state['step'] ?? null) === 'awaiting_branch' && str_starts_with($listId, 'branch_page_')) {
                $page = (int) str_replace('branch_page_', '', $listId);
                if ($page < 1) {
                    $page = 1;
                }

                $state['branch_page'] = $page;
                $this->setConversationState($from, $state);
                $this->sendBranchMenu($from, $state['company_id'], $page);
                return response()->json(['status' => 'ok']);
            }

            if (($state['step'] ?? null) === 'awaiting_branch' && str_starts_with($listId, 'branch_')) {
                $branchId = (int) str_replace('branch_', '', $listId);

                $branch = DB::table('branch')
                    ->select('branch_id', 'branch_name')
                    ->where('branch_id', $branchId)
                    ->where('company_id', $state['company_id'] ?? 0)
                    ->first();

                if (!$branch) {
                    $this->sendText($from, "Invalid branch selection. Please select from the list.");
                    return response()->json(['status' => 'ok']);
                }

                $state['branch_id'] = (int) $branch->branch_id;
                $state['branch_name'] = $branch->branch_name;
                $state['step'] = 'awaiting_terminal_selection';
                $state['terminal_page'] = 1;
                $this->setConversationState($from, $state);
                $this->sendTerminalMenu($from, $state['branch_id'], 1);
                return response()->json(['status' => 'ok']);
            }

            if (($state['step'] ?? null) === 'awaiting_terminal_selection' && str_starts_with($listId, 'terminal_page_')) {
                $page = (int) str_replace('terminal_page_', '', $listId);
                if ($page < 1) {
                    $page = 1;
                }

                $state['terminal_page'] = $page;
                $this->setConversationState($from, $state);
                $this->sendTerminalMenu($from, $state['branch_id'], $page);
                return response()->json(['status' => 'ok']);
            }

            if (($state['step'] ?? null) === 'awaiting_terminal_selection' && $listId === 'terminal_all') {
                $state['terminal'] = '0';
                $state['terminal_name'] = 'All Terminals';
                $state['step'] = 'awaiting_month';
                $this->setConversationState($from, $state);
                $this->sendLastSixMonthsMenu($from);
                return response()->json(['status' => 'ok']);
            }

            if (($state['step'] ?? null) === 'awaiting_terminal_selection' && str_starts_with($listId, 'terminal_')) {
                $terminalId = (int) str_replace('terminal_', '', $listId);

                $terminal = DB::table('terminal_details')
                    ->select('terminal_id', 'terminal_name')
                    ->where('terminal_id', $terminalId)
                    ->where('branch_id', $state['branch_id'] ?? 0)
                    ->first();

                if (!$terminal) {
                    $this->sendText($from, "Invalid terminal selection. Please select from the list.");
                    return response()->json(['status' => 'ok']);
                }

                $state['terminal'] = (string) $terminal->terminal_id;
                $state['terminal_name'] = $terminal->terminal_name;
                $state['step'] = 'awaiting_month';
                $this->setConversationState($from, $state);
                $this->sendLastSixMonthsMenu($from);
                return response()->json(['status' => 'ok']);
            }

            if (($state['step'] ?? null) === 'awaiting_month' && str_starts_with($listId, 'month_')) {
                $monthKey = str_replace('month_', '', $listId); // YYYY-MM

                if (!preg_match('/^\d{4}-\d{2}$/', $monthKey)) {
                    $this->sendText($from, "Invalid month selection. Please select from the list.");
                    return response()->json(['status' => 'ok']);
                }

                $state['from_date'] = Carbon::createFromFormat('Y-m', $monthKey)->startOfMonth()->format('Y-m-d');
                $state['to_date'] = Carbon::createFromFormat('Y-m', $monthKey)->endOfMonth()->format('Y-m-d');
                if (!isset($state['terminal'])) {
                    $state['terminal'] = '0';
                    $state['terminal_name'] = 'All Terminals';
                }
                $this->processReportRequest($from, $state);
                $this->clearConversationState($from);
                return response()->json(['status' => 'ok']);
            }
        }

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

            // Always allow user to restart flow from any step.
            if ($text == 'hi' || $text == 'hello') {
                $this->clearConversationState($from);
                $this->sendMenu($from);
                return response()->json(['status' => 'ok']);
            }

            if (($state['step'] ?? null) === 'awaiting_mobile') {
                $mobile = $this->normalizeMobile($textRaw);

                if (!$mobile) {
                    $this->sendText($from, "Invalid mobile number. Please enter a valid registered mobile number.");
                    return response()->json(['status' => 'ok']);
                }

                if (($state['report_type'] ?? '') === 'fbr_report') {
                    $company = $this->findCompanyByWhatsAppNumber($mobile, $textRaw);

                    if (!$company) {
                        $this->sendText($from, "This mobile number is not registered for WhatsApp reports.");
                        return response()->json(['status' => 'ok']);
                    }

                    $branches = DB::table('branch')
                        ->select('branch_id', 'branch_name')
                        ->where('company_id', $company->company_id)
                        ->orderBy('branch_name')
                        ->get();

                    if ($branches->isEmpty()) {
                        $this->sendText($from, "No branches found for this company.");
                        return response()->json(['status' => 'ok']);
                    }

                    $state['mobile'] = $mobile;
                    $state['company_id'] = (int) $company->company_id;
                    $state['company_name'] = $company->name;
                    $state['step'] = 'awaiting_branch';
                    $state['branch_page'] = 1;
                    $this->setConversationState($from, $state);
                    $this->sendBranchMenu($from, $company->company_id, 1);
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

    private function sendBranchMenu($number, $companyId, $page = 1)
    {
        $page = max(1, (int) $page);
        $offset = ($page - 1) * self::BRANCH_PAGE_SIZE;

        $totalBranches = DB::table('branch')
            ->where('company_id', $companyId)
            ->count();

        $branches = DB::table('branch')
            ->select('branch_id', 'branch_name')
            ->where('company_id', $companyId)
            ->orderBy('branch_name')
            ->skip($offset)
            ->take(self::BRANCH_PAGE_SIZE)
            ->get();

        $rows = [];
        foreach ($branches as $branch) {
            $rows[] = [
                "id" => "branch_" . $branch->branch_id,
                "title" => $branch->branch_name,
                "description" => "Select this branch",
            ];
        }

        $totalPages = (int) ceil($totalBranches / self::BRANCH_PAGE_SIZE);
        if ($totalPages > 1 && $page > 1) {
            $rows[] = [
                "id" => "branch_page_" . ($page - 1),
                "title" => "Previous Branches",
                "description" => "Page " . ($page - 1) . " of " . $totalPages,
            ];
        }

        if ($totalPages > 1 && $page < $totalPages) {
            $rows[] = [
                "id" => "branch_page_" . ($page + 1),
                "title" => "More Branches",
                "description" => "Page " . ($page + 1) . " of " . $totalPages,
            ];
        }

        $payload = [
            "messaging_product" => "whatsapp",
            "to" => $number,
            "type" => "interactive",
            "interactive" => [
                "type" => "list",
                "body" => [
                    "text" => "Please select branch:"
                ],
                "action" => [
                    "button" => "Select Branch",
                    "sections" => [
                        [
                            "title" => "Branches (Page {$page}/" . max($totalPages, 1) . ")",
                            "rows" => $rows
                        ]
                    ]
                ]
            ]
        ];

        $this->sendRequest($payload);
    }

    private function sendLastSixMonthsMenu($number)
    {
        $rows = [];
        foreach ($this->getLastSixMonthsOldestFirst() as $month) {
            $rows[] = [
                "id" => "month_" . $month['key'],
                "title" => $month['label'],
                "description" => "Monthly report",
            ];
        }

        $payload = [
            "messaging_product" => "whatsapp",
            "to" => $number,
            "type" => "interactive",
            "interactive" => [
                "type" => "list",
                "body" => [
                    "text" => "Please select month:"
                ],
                "action" => [
                    "button" => "Select Month",
                    "sections" => [
                        [
                            "title" => "Last 6 Months",
                            "rows" => $rows
                        ]
                    ]
                ]
            ]
        ];

        $this->sendRequest($payload);
    }

    private function sendTerminalMenu($number, $branchId, $page = 1)
    {
        $page = max(1, (int) $page);
        $offset = ($page - 1) * self::TERMINAL_PAGE_SIZE;

        $totalTerminals = DB::table('terminal_details')
            ->where('branch_id', $branchId)
            ->count();

        $terminals = DB::table('terminal_details')
            ->select('terminal_id', 'terminal_name')
            ->where('branch_id', $branchId)
            ->orderBy('terminal_name')
            ->skip($offset)
            ->take(self::TERMINAL_PAGE_SIZE)
            ->get();

        $rows = [];
        if ($page === 1) {
            $rows[] = [
                "id" => "terminal_all",
                "title" => "All Terminals",
                "description" => "Generate report for all terminals",
            ];
        }

        foreach ($terminals as $terminal) {
            $rows[] = [
                "id" => "terminal_" . $terminal->terminal_id,
                "title" => $terminal->terminal_name,
                "description" => "Select this terminal",
            ];
        }

        $totalPages = (int) ceil($totalTerminals / self::TERMINAL_PAGE_SIZE);
        if ($totalPages > 1 && $page > 1) {
            $rows[] = [
                "id" => "terminal_page_" . ($page - 1),
                "title" => "Previous Terminals",
                "description" => "Page " . ($page - 1) . " of " . $totalPages,
            ];
        }

        if ($totalPages > 1 && $page < $totalPages) {
            $rows[] = [
                "id" => "terminal_page_" . ($page + 1),
                "title" => "More Terminals",
                "description" => "Page " . ($page + 1) . " of " . $totalPages,
            ];
        }

        $payload = [
            "messaging_product" => "whatsapp",
            "to" => $number,
            "type" => "interactive",
            "interactive" => [
                "type" => "list",
                "body" => [
                    "text" => "Please select terminal:"
                ],
                "action" => [
                    "button" => "Select Terminal",
                    "sections" => [
                        [
                            "title" => "Terminals (Page {$page}/" . max($totalPages, 1) . ")",
                            "rows" => $rows
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

        $isFbrFlow = $reportType === 'fbr_report';

        if (
            !$reportType || !$mobile || !$fromDate || !$toDate ||
            ($isFbrFlow && empty($state['company_id'])) ||
            ($isFbrFlow && empty($state['branch_id'])) ||
            (!$isFbrFlow && !$terminal)
        ) {
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
        $report = $this->buildReportAndGetPdf($reportType, $state);

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
            $isFbrFlow ? ($state['terminal_name'] ?? 'All Terminals') : $terminal
        );
    }

    private function buildReportAndGetPdf($reportType, array $state)
    {
        Log::info('Report request captured', [
            'report_type' => $reportType,
            'mobile' => $state['mobile'] ?? null,
            'terminal' => $state['terminal'] ?? null,
            'branch_id' => $state['branch_id'] ?? null,
            'from_date' => $state['from_date'] ?? null,
            'to_date' => $state['to_date'] ?? null,
        ]);

        if ($reportType === 'fbr_report') {
            return $this->buildFbrReportAndGetPdf($state);
        }

        if ($reportType !== 'sales_report') {
            return null;
        }

        $fromDate = $state['from_date'];
        $terminal = $state['terminal'];
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

    private function buildFbrReportAndGetPdf(array $state)
    {
        $company = DB::table('company')
            ->select('company_id', 'name', 'ptcl_contact', 'address', 'logo')
            ->where('company_id', $state['company_id'])
            ->first();

        $branch = DB::table('branch')
            ->select('branch_id', 'branch_name')
            ->where('branch_id', $state['branch_id'])
            ->where('company_id', $state['company_id'])
            ->first();

        if (!$company || !$branch) {
            return null;
        }

        $reportModel = new ReportModel();
        $pdf = new pdfClass();
        $pdf->AliasNbPages();
        $pdf->AddPage();

        $fromDate = $state['from_date'];
        $toDate = $state['to_date'];
        $fromLabel = date('F-d-Y', strtotime($fromDate));
        $toLabel = date('F-d-Y', strtotime($toDate));

        //first row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 0, '', 0, 0);
        $pdf->Cell(105, 0, "Company Name:", 0, 0, 'L');
        $pdf->Cell(50, 0, "", 0, 1, 'L');

        //second row
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(35, 0, '', 0, 0);
        
        $logoPath = public_path('storage/images/company/' . $company->logo);
        $logoUrl = asset('storage/images/company/' . $company->logo);
        if (file_exists($logoPath) && !is_dir($logoPath)) {
            $pdf->Image($logoPath, 12, 10, -200);
        } else {
            $pdf->Image($logoUrl, 12, 10, -200);
        }
        $pdf->Cell(105, 12, "FBR REPORT", 0, 0, 'L');
        $pdf->Cell(50, 0, "", 0, 1, 'R');

        $qrPath = public_path('storage/images/company/qrcode.png');
        $qrUrl = asset('storage/images/company/qrcode.png');
        if (file_exists($qrPath) && !is_dir($qrPath)) {
            $pdf->Image($qrPath, 175, 10, -200);
        } else {
            $pdf->Image($qrUrl, 175, 10, -200);
        }

        //third row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 25, '', 0, 0);
        $pdf->Cell(105, 25, "Contact Number:", 0, 0, 'L');
        $pdf->Cell(50, 25, "", 0, 1, 'L');

        //forth row
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(35, -15, '', 0, 0);
        $pdf->Cell(105, -15, $company->ptcl_contact, 0, 0, 'L');
        $pdf->Cell(50, -15, "", 0, 1, 'L');

        //fifth row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 28, '', 0, 0);
        $pdf->Cell(105, 28, "Company Address:", 0, 0, 'L');
        $pdf->Cell(50, 28, "", 0, 1, 'L');

        //sixth row
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(35, -18, '', 0, 0);
        $pdf->Cell(105, -18, $company->address, 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(50, -18, "Generate Date:  " . date('Y-m-d'), 0, 1, 'R');

        //filter section
        $pdf->ln(12);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(190, 10, $fromLabel . ' through ' . $toLabel, 0, 1, 'C');

        //report name
        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(190, 10, 'FBR Report', 'B,T', 1, 'L');
        $pdf->ln(1);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(10, 7, 'S.No', 'B', 0, 'L');
        $pdf->Cell(30, 7, 'Sales ID', 'B', 0, 'C');
        $pdf->Cell(45, 7, 'FBR Inv Number', 'B', 0, 'L');
        $pdf->Cell(25, 7, 'Date', 'B', 0, 'C');
        $pdf->Cell(25, 7, 'Sales', 'B', 0, 'C');
        $pdf->Cell(20, 7, 'S.Tax', 'B', 0, 'C');
        $pdf->Cell(35, 7, 'Total Amount', 'B', 1, 'C');

        $totalActual = 0;
        $totalTax = 0;
        $totalAmount = 0;

        $selectedTerminalId = (int) ($state['terminal'] ?? 0);
        if ($selectedTerminalId > 0) {
            $terminals = DB::table('terminal_details')
                ->select('terminal_id', 'terminal_name')
                ->where('branch_id', $branch->branch_id)
                ->where('terminal_id', $selectedTerminalId)
                ->get();
        } else {
            $terminals = $reportModel->get_terminals_by_branch($branch->branch_id);
        }

        if ($terminals->isEmpty()) {
            return null;
        }

        foreach ($terminals as $terminal) {
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(190, 8, 'Terminal: ' . $terminal->terminal_name, 0, 1, 'L');

            $details = $reportModel->sales($terminal->terminal_id, $fromDate, $toDate);
            foreach ($details as $index => $value) {
                $actualAmount = $value->actual_amount == 0
                    ? ($value->total_amount - $value->sales_tax_amount)
                    : $value->actual_amount;

                $totalActual += $actualAmount;
                $totalTax += $value->sales_tax_amount;
                $totalAmount += $value->total_amount;

                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(10, 6, $index + 1, 0, 0, 'L');
                $pdf->Cell(30, 6, $value->id, 0, 0, 'C');
                $pdf->Cell(45, 6, $value->fbrInvNumber, 0, 0, 'L');
                $pdf->Cell(25, 6, date('d M Y', strtotime($value->date)), 0, 0, 'C');
                $pdf->Cell(25, 6, number_format($actualAmount, 2), 0, 0, 'C');
                $pdf->Cell(20, 6, number_format($value->sales_tax_amount, 2), 0, 0, 'C');
                $pdf->Cell(35, 6, number_format($value->total_amount, 2), 0, 1, 'C');
            }
        }

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(110, 7, 'Total', 'B,T', 0, 'R');
        $pdf->Cell(25, 7, number_format($totalActual, 2), 'B,T', 0, 'C');
        $pdf->Cell(20, 7, number_format($totalTax, 2), 'B,T', 0, 'C');
        $pdf->Cell(35, 7, number_format($totalAmount, 2), 'B,T', 1, 'C');

        $safeCompany = preg_replace('/[\\\\\/:*?"<>|]+/', ' ', $company->name);
        $safeBranch = preg_replace('/[\\\\\/:*?"<>|]+/', ' ', $branch->branch_name);
        $safeTerminal = preg_replace('/[\\\\\/:*?"<>|]+/', ' ', $state['terminal_name'] ?? 'All_Terminals');
        $period = date('Ymd', strtotime($fromDate)) . '_' . date('Ymd', strtotime($toDate));
        $generatedAt = date('Ymd_His');
        $fileName = 'FBR_REPORT_' . $period . '_' . trim($safeCompany) . '_' . trim($safeBranch) . '_' . trim($safeTerminal) . '_' . $generatedAt . '.pdf';
        $dir = storage_path('app/public/pdfs');
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $filePath = $dir . DIRECTORY_SEPARATOR . $fileName;
        $pdf->Output($filePath, 'F');

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
                            ["type" => "text", "text" => $mobile],
                            ["type" => "text", "text" => $reportName],
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

    private function findCompanyByWhatsAppNumber($normalizedMobile, $rawInput)
    {
        $candidates = array_unique([
            trim($rawInput),
            $normalizedMobile,
            '+' . $normalizedMobile,
            '0' . $normalizedMobile,
        ]);

        foreach ($candidates as $candidate) {
            $company = DB::table('company')
                ->select('company_id', 'name', 'whatsapp_number')
                ->where('whatsapp_number', $candidate)
                ->first();

            if ($company) {
                return $company;
            }
        }

        return null;
    }

    private function getLastSixMonthsOldestFirst()
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = [
                'key' => $month->format('Y-m'),
                'label' => strtoupper($month->format('M-Y')),
            ];
        }

        return $months;
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

    private function acquireInboundMessageLock(string $messageId): bool
    {
        return Cache::add($this->inboundMessageLockKey($messageId), true, now()->addMinutes(2));
    }

    private function inboundMessageLockKey(string $messageId): string
    {
        return "wa_inbound_msg_lock:{$messageId}";
    }
}

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
use Throwable;

class WhatsAppController extends Controller
{
    private const STATE_TTL_SECONDS = 1800;
    private const BRANCH_PAGE_SIZE = 8;
    private const TERMINAL_PAGE_SIZE = 8;
    private const CHAT_SESSION_IDLE_MINUTES = 30;

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
        $this->recordInboundMessage($from, $message);
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
                $this->sendTerminalMenu($from, $state['branch_id'], 1, $this->reportAllowsAllTerminals($state['report_type'] ?? null));
                return response()->json(['status' => 'ok']);
            }

            if (($state['step'] ?? null) === 'awaiting_terminal_selection' && str_starts_with($listId, 'terminal_page_')) {
                $page = (int) str_replace('terminal_page_', '', $listId);
                if ($page < 1) {
                    $page = 1;
                }

                $state['terminal_page'] = $page;
                $this->setConversationState($from, $state);
                $this->sendTerminalMenu($from, $state['branch_id'], $page, $this->reportAllowsAllTerminals($state['report_type'] ?? null));
                return response()->json(['status' => 'ok']);
            }

            if (($state['step'] ?? null) === 'awaiting_terminal_selection' && $listId === 'terminal_all') {
                if (!$this->reportAllowsAllTerminals($state['report_type'] ?? null)) {
                    $this->sendText($from, "Invalid terminal selection. Please select from the list.");
                    return response()->json(['status' => 'ok']);
                }

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

            if ($buttonId == "fbr_report" || $buttonId == "sales_report" || $buttonId == "item_sale_database") {
                $this->startReportFlowUsingSenderNumber($from, $buttonId);
                return response()->json(['status' => 'ok']);
            }
        }

        // TEXT MESSAGE
        if (isset($message['text'])) {
            $textRaw = trim($message['text']['body']);
            $text = strtolower($textRaw);

            // Always allow user to restart flow from any step.
            if ($text == 'sabify') {  // if ($text == 'hi' || $text == 'hello') {
                $this->clearConversationState($from);
                $this->sendMenu($from);
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
                        ],
                        [
                            "type" => "reply",
                            "reply" => [
                                "id" => "item_sale_database",
                                "title" => "Item Sale Database"
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

    private function sendTerminalMenu($number, $branchId, $page = 1, $allowAll = true)
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
        if ($allowAll && $page === 1) {
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
        $response = Http::withToken($this->token)
            ->post("https://graph.facebook.com/v17.0/{$this->phoneId}/messages", $payload);

        $this->recordOutboundMessage($payload, $response);

        return $response;
    }

    private function startReportFlowUsingSenderNumber(string $from, string $reportType): void
    {
        $mobile = $this->normalizeMobile($from);

        if (!$mobile) {
            $this->clearConversationState($from);
            $this->sendText($from, "Invalid mobile number. Please enter a valid registered mobile number.");
            return;
        }

        $company = $this->findCompanyByWhatsAppNumber($mobile, $from);

        if (!$company) {
            $this->clearConversationState($from);
            $this->sendText($from, "This mobile number is not registered for WhatsApp reports.");
            return;
        }

        $state = [
            'report_type' => $reportType,
            'mobile' => $mobile,
            'company_id' => (int) $company->company_id,
            'company_name' => $company->name,
        ];

        $branches = DB::table('branch')
            ->select('branch_id', 'branch_name')
            ->where('company_id', $company->company_id)
            ->orderBy('branch_name')
            ->get();

        if ($branches->isEmpty()) {
            $this->clearConversationState($from);
            $this->sendText($from, "No branches found for this company.");
            return;
        }

        $state['step'] = 'awaiting_branch';
        $state['branch_page'] = 1;
        $this->setConversationState($from, $state);
        $this->sendBranchMenu($from, $company->company_id, 1);
    }

    private function processReportRequest($to, array $state)
    {
        try {
            $reportType = $state['report_type'] ?? null;
            $mobile = $state['mobile'] ?? null;
            $terminal = $state['terminal'] ?? null;
            $fromDate = $state['from_date'] ?? null;
            $toDate = $state['to_date'] ?? null;

            $isBranchBasedFlow = $this->isBranchBasedReport($reportType);

            if (
                !$reportType || !$mobile || !$fromDate || !$toDate ||
                ($isBranchBasedFlow && empty($state['company_id'])) ||
                ($isBranchBasedFlow && empty($state['branch_id'])) ||
                (!$isBranchBasedFlow && !$terminal)
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
            $reportName = $this->getReportDisplayName($reportType);
            $filename = $report['filename'] ?? ($reportName . '.pdf');

            $this->sendTemplateWithDocument(
                $to,
                $templateName,
                $language,
                $report['url'],
                $filename,
                $reportName,
                $mobile,
                $isBranchBasedFlow ? ($state['terminal_name'] ?? 'All Terminals') : $terminal
            );

            // Re-open main options so user can continue without sending "hi".
            $this->sendMenu($to);
        } catch (Throwable $e) {
            Log::error('Failed to process WhatsApp report request', [
                'to' => $to,
                'state' => $state,
                'error' => $e->getMessage(),
            ]);
            $this->sendText($to, "We could not generate your report right now. Please try again.");
        }
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

        if ($reportType === 'item_sale_database') {
            return $this->buildItemSaleDatabaseReportAndGetPdf($state);
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
            // report model returns array from DB::select; normalize to collection.
            $terminals = collect($reportModel->get_terminals_by_branch($branch->branch_id));
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

    private function buildItemSaleDatabaseReportAndGetPdf(array $state)
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
        $fromDate = $state['from_date'];
        $toDate = $state['to_date'];
        $selectedTerminalId = (int) ($state['terminal'] ?? 0);

        if ($selectedTerminalId > 0) {
            $terminals = collect($reportModel->get_terminals_byid($selectedTerminalId))
                ->where('branch_id', $branch->branch_id)
                ->values();
        } else {
            $terminals = collect($reportModel->get_terminals_by_branch($branch->branch_id));
        }

        if ($terminals->isEmpty()) {
            return null;
        }

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'jameel-noori-nastaleeq',
            'default_font_size' => 12,
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'margin_header' => 10,
            'margin_footer' => 10,
            'direction' => 'rtl',
        ]);

        $mpdf->fontdata['jameel-noori-nastaleeq'] = [
            'R' => 'Jameel-Noori-Nastaleeq.ttf',
            'useOTL' => 0xFF,
        ];

        $companyLogo = asset('storage/images/company/' . $company->logo);
        $qrImage = asset('storage/images/company/qrcode.png');
        $branchLabel = ' (' . $branch->branch_name . ') ';

        $html = '
    <html dir="ltr">
    <head>
        <style>
            body { font-family: jameel-noori-nastaleeq; }
            h1 { line-height: 0.6; }
            .text-bold { font-weight: bold; }
            p { font-size: 14px; line-height: 0.9; margin: 5px 0; }
            h2 { font-size: 14px; line-height: 0.6; color: green; }
            .text-center { text-align: center; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            td { vertical-align: top; padding: 5px; }
            .company-info { width: 50%; }
            .qr-section { width: 50%; }
            .header-row td {
                font-size: 18px;
                font-weight: bold;
                padding: 10px;
                text-align: center;
                background-color: #f8f9fa;
                border-bottom: 2px solid #dee2e6;
            }
            thead th {
                background-color: #1a4567;
                color: white;
                padding: 12px 8px;
                text-align: center;
                font-weight: bold;
                border: 1px solid #0d2235;
                font-size: 14px;
            }
            tbody td {
                padding: 8px;
                text-align: center;
                border: 1px solid #dee2e6;
                font-size: 13px;
            }
            tbody tr:nth-child(even) { background-color: #f8f9fa; }
            .summary {
                margin-top: 30px;
                page-break-before: avoid;
            }
            .void { background-color: #ffcccc; }
            .return { background-color: #ffd9cc; }
            .normal { background-color: #f2f2f2; }
        </style>
    </head>
    <body>';

        $html .= '
        <table>
            <tr>
                <td class="company-info">
                    <table style="width: auto;">
                        <tr>
                            <td>
                                <img width="100" height="100" src="' . $companyLogo . '" alt="">
                            </td>
                            <td style="padding-left: 16px;">
                                <p>Company Name:</p>
                                <h4 class="text-bold">' . e($company->name) . '</h4>
                                <p>Contact Number</p>
                                <p class="text-bold">0' . e($company->ptcl_contact) . '</p>
                                <p>Company Address</p>
                                <p class="text-bold">' . e($company->address) . '</p>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="qr-section" style="width: 100px; text-align: right;">
                    <img width="100" height="100" src="' . $qrImage . '" alt="" style="margin-left: auto; display: block;">
                </td>
            </tr>
        </table>';

        $html .= '<h4 style="text-align: center;">Date: ' . date('Y-m-d', strtotime($fromDate)) . ' From ' . date('Y-m-d', strtotime($toDate)) . ' To </h4>';
        $html .= '<h2 style="text-align: center;">Item Sale Database' . e($branchLabel) . '</h2>';

        $grandTotalSales = 0;
        $grandTotalQty = 0;
        $grandTotalDiscount = 0;
        $departmentSales = [];
        $departmentQty = [];

        foreach ($terminals as $terminal) {
            $html .= '<h3 style="text-align: center;background-color: #1a4567;color: #FFFFFF;">Terminal: ' . e($terminal->terminal_name) . '</h3>';

            $modes = $reportModel->itemSalesOrderMode($fromDate, $toDate, $terminal->terminal_id, 'all', 'all');

            if (empty($modes)) {
                $html .= '<div style="text-align: center; padding: 20px; background-color: #f9f9f9; border: 1px solid #ddd; margin: 10px 0;">
                    <p style="color: #666; font-size: 16px; margin: 0;">Modes are empty</p>
                    <p style="color: #999; font-size: 14px; margin: 5px 0 0 0;">No Data Found</p>
                </div>';
                continue;
            }

            $totalDiscount = $reportModel->getTotalDiscounts($fromDate, $toDate, $terminal->terminal_id, 'all', 'all');
            $grandTotalDiscount += (float) ($totalDiscount[0]->totaldiscount ?? 0);

            foreach ($modes as $mode) {
                $html .= '<h5 style="text-align: center;background-color: #ddd;color: #000;margin-bottom: -2px;padding: 12px 8px;">' . e($mode->ordermode) . '</h5>';

                $html .= '
            <table>
                <thead>
                    <tr>
                        <th>Item code</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Amount</th>
                        <th>COGS</th>
                        <th>Margin</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>';

                $details = $reportModel->itemsale_details(
                    $fromDate,
                    $toDate,
                    $terminal->terminal_id,
                    $mode->order_mode_id,
                    '',
                    '',
                    'all',
                    'all',
                    ''
                );

                if (empty($details)) {
                    $html .= '<tr><td colspan="8" style="text-align: center;">No data found</td></tr>';
                }

                $totalCount = 0;
                $totalQty = 0;
                $totalAmount = 0;
                $totalCost = 0;
                $totalMargin = 0;

                if (!empty($details)) {
                    foreach ($details as $item) {
                        $itemQty = 0;
                        $rowClass = $item->void_receipt == 1 ? 'void' : ($item->is_sale_return == 1 ? 'return' : 'normal');

                        if ((int) $state['company_id'] === 74) {
                            $itemQty += ($item->qty * $item->weight_qty);
                        } else {
                            $itemQty = $item->qty;
                        }

                        $html .= sprintf(
                            '
                        <tr class="%s">
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                        </tr>',
                            $rowClass,
                            e($item->code),
                            e($item->product_name),
                            number_format($itemQty),
                            number_format($item->price),
                            number_format($item->amount),
                            number_format($item->cost),
                            number_format($item->amount - $item->cost),
                            e($item->order_status_name)
                        );

                        if ($item->void_receipt != 1) {
                            $totalCount++;
                            $totalQty += $itemQty;
                            $totalAmount += $item->amount;
                            $totalCost += $item->cost;
                            $totalMargin += ($item->amount - $item->cost);
                            $grandTotalQty += $itemQty;
                            $grandTotalSales += $item->amount;

                            $deptName = $item->department_name ?? 'Other';
                            if (!isset($departmentSales[$deptName])) {
                                $departmentSales[$deptName] = 0;
                            }
                            if (!isset($departmentQty[$deptName])) {
                                $departmentQty[$deptName] = 0;
                            }
                            $departmentSales[$deptName] += $item->amount;
                            $departmentQty[$deptName] += $itemQty;
                        }
                    }
                } else {
                    $html .= '<tr><td colspan="8" style="text-align: center;">No data found</td></tr>';
                }

                $html .= sprintf(
                    '
                <tr style="font-weight: bold;background-color: #00000;color: #FFFFFF;">
                    <td colspan="2" style="color: #FFFFFF;font-weight: bold;">Total Items: %s</td>
                    <td style="color: #FFFFFF;font-weight: bold;">%s</td>
                    <td style="color: #FFFFFF;font-weight: bold;">-</td>
                    <td style="color: #FFFFFF;font-weight: bold;">%s</td>
                    <td style="color: #FFFFFF;font-weight: bold;">%s</td>
                    <td style="color: #FFFFFF;font-weight: bold;">%s</td>
                    <td style="color: #FFFFFF;font-weight: bold;">-</td>
                </tr>',
                    $totalCount,
                    number_format($totalQty),
                    number_format($totalAmount),
                    number_format($totalCost),
                    number_format($totalMargin)
                );

                $html .= '</tbody></table>';
            }
        }

        $html .= '
        <div class="summary">
            <h3 style="text-align: center; background-color: #1a4567; color: #FFFFFF; padding: 10px;">SUMMARY</h3>
            <table style="width: 60%; margin: 20px auto;">
                <tr style="background-color: #f8f9fa;">
                    <td style="padding: 10px; font-weight: bold; border: 1px solid #dee2e6;">Total Items</td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">' . number_format($grandTotalQty, 2) . '</td>
                </tr>
                <tr style="background-color: #ffffff;">
                    <td style="padding: 10px; font-weight: bold; border: 1px solid #dee2e6;">Total Sales</td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">' . number_format($grandTotalSales, 2) . '</td>
                </tr>
                <tr style="background-color: #f8f9fa;">
                    <td style="padding: 10px; font-weight: bold; border: 1px solid #dee2e6;">Total Discount</td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">' . number_format($grandTotalDiscount, 2) . '</td>
                </tr>
                <tr style="background-color: #ffffff;">
                    <td style="padding: 10px; font-weight: bold; border: 1px solid #dee2e6;">Discounted Sales</td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">' . number_format($grandTotalSales - $grandTotalDiscount, 2) . '</td>
                </tr>
            </table>
            <h4 style="text-align: center; margin-top: 20px; color: #1a4567;">Department Wise Sales</h4>
            <table style="width: 60%; margin: 10px auto;">
                <thead>
                    <tr style="background-color: #1a4567; color: #FFFFFF;">
                        <th style="padding: 10px; text-align: left; border: 1px solid #0d2235;font-weight: bold;">Department</th>
                        <th style="padding: 10px; text-align: right; border: 1px solid #0d2235;font-weight: bold;">Items</th>
                        <th style="padding: 10px; text-align: right; border: 1px solid #0d2235;font-weight: bold;">Amount</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($departmentSales as $dept => $amount) {
            $html .= '
                <tr style="background-color: #f8f9fa;">
                    <td style="padding: 8px; border: 1px solid #dee2e6;">' . e($dept) . '</td>
                    <td style="padding: 8px; text-align: right; border: 1px solid #dee2e6;">' . number_format($departmentQty[$dept] ?? 0, 2) . '</td>
                    <td style="padding: 8px; text-align: right; border: 1px solid #dee2e6;">' . number_format($amount, 2) . '</td>
                </tr>';
        }

        $html .= '
                <tr style="background-color: #1a4567; color: #FFFFFF; font-weight: bold;">
                    <td style="padding: 10px; border: 1px solid #0d2235;color: #FFFFFF;">Total</td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #0d2235;color: #FFFFFF;">' . number_format(array_sum($departmentQty), 2) . '</td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #0d2235;color: #FFFFFF;">' . number_format(array_sum($departmentSales), 2) . '</td>
                </tr>
                </tbody>
            </table>
        </div>
        </body>
        </html>';

        $mpdf->WriteHTML($html);

        $safeCompany = preg_replace('/[\\\\\/:*?"<>|]+/', ' ', $company->name);
        $safeBranch = preg_replace('/[\\\\\/:*?"<>|]+/', ' ', $branch->branch_name);
        $safeTerminal = preg_replace('/[\\\\\/:*?"<>|]+/', ' ', $state['terminal_name'] ?? 'All_Terminals');
        $period = date('Ymd', strtotime($fromDate)) . '_' . date('Ymd', strtotime($toDate));
        $generatedAt = date('Ymd_His');
        $fileName = 'ITEM_SALE_DATABASE_' . $period . '_' . trim($safeCompany) . '_' . trim($safeBranch) . '_' . trim($safeTerminal) . '_' . $generatedAt . '.pdf';
        $dir = storage_path('app/public/pdfs');
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $filePath = $dir . DIRECTORY_SEPARATOR . $fileName;
        $mpdf->Output($filePath, 'F');

        return [
            'url' => 'https://retail.sabsoft.com.pk/storage/pdfs/' . rawurlencode($fileName),
            'filename' => $fileName,
        ];
    }

    private function isBranchBasedReport(?string $reportType): bool
    {
        return in_array($reportType, ['fbr_report', 'item_sale_database'], true);
    }

    private function reportAllowsAllTerminals(?string $reportType): bool
    {
        return in_array($reportType, ['fbr_report', 'item_sale_database'], true);
    }

    private function getReportDisplayName(?string $reportType): string
    {
        if ($reportType === 'fbr_report') {
            return 'FBR Report';
        }

        if ($reportType === 'item_sale_database') {
            return 'Item Sale Database';
        }

        return 'Sales Report';
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

    private function recordInboundMessage(string $from, array $message): void
    {
        try {
            $now = now();
            $parsed = $this->extractInboundMessageDetails($message);

            $messageId = $parsed['wa_message_id'] ?? null;
            if ($messageId && DB::table('wa_chat_messages')->where('wa_message_id', $messageId)->exists()) {
                return;
            }

            $contactId = $this->upsertChatContact($from, $now);
            $sessionId = $this->resolveChatSessionId(
                $contactId,
                'user',
                $parsed['asked_text'],
                $now
            );

            DB::table('wa_chat_messages')->insert([
                'session_id' => $sessionId,
                'contact_id' => $contactId,
                'direction' => 'inbound',
                'wa_message_id' => $messageId,
                'message_type' => $parsed['message_type'],
                'asked_text' => $parsed['asked_text'],
                'button_id' => $parsed['button_id'],
                'button_title' => $parsed['button_title'],
                'list_id' => $parsed['list_id'],
                'list_title' => $parsed['list_title'],
                'payload' => json_encode($message, JSON_UNESCAPED_UNICODE),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('wa_contacts')
                ->where('id', $contactId)
                ->update([
                    'total_inbound' => DB::raw('total_inbound + 1'),
                    'updated_at' => $now,
                ]);
        } catch (Throwable $e) {
            Log::warning('Failed to log inbound WhatsApp message', [
                'from' => $from,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function recordOutboundMessage(array $payload, $response = null): void
    {
        try {
            $to = isset($payload['to']) ? trim((string) $payload['to']) : '';
            if ($to === '') {
                return;
            }

            $now = now();
            $parsed = $this->extractOutboundMessageDetails($payload, $response);
            $messageId = $parsed['wa_message_id'] ?? null;

            if ($messageId && DB::table('wa_chat_messages')->where('wa_message_id', $messageId)->exists()) {
                return;
            }

            $contactId = $this->upsertChatContact($to, $now);
            $sessionId = $this->resolveChatSessionId(
                $contactId,
                'business',
                $parsed['asked_text'],
                $now
            );

            DB::table('wa_chat_messages')->insert([
                'session_id' => $sessionId,
                'contact_id' => $contactId,
                'direction' => 'outbound',
                'wa_message_id' => $messageId,
                'message_type' => $parsed['message_type'],
                'asked_text' => $parsed['asked_text'],
                'button_id' => null,
                'button_title' => null,
                'list_id' => null,
                'list_title' => null,
                'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('wa_contacts')
                ->where('id', $contactId)
                ->update([
                    'total_outbound' => DB::raw('total_outbound + 1'),
                    'updated_at' => $now,
                ]);
        } catch (Throwable $e) {
            Log::warning('Failed to log outbound WhatsApp message', [
                'to' => $payload['to'] ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function extractInboundMessageDetails(array $message): array
    {
        $messageType = isset($message['type']) ? (string) $message['type'] : 'unknown';
        $askedText = null;
        $buttonId = null;
        $buttonTitle = null;
        $listId = null;
        $listTitle = null;

        if (isset($message['text']['body'])) {
            $askedText = trim((string) $message['text']['body']);
        }

        if (isset($message['interactive']['button_reply'])) {
            $messageType = 'interactive_button_reply';
            $buttonId = $message['interactive']['button_reply']['id'] ?? null;
            $buttonTitle = $message['interactive']['button_reply']['title'] ?? null;
            $askedText = $buttonTitle ?? $askedText;
        }

        if (isset($message['interactive']['list_reply'])) {
            $messageType = 'interactive_list_reply';
            $listId = $message['interactive']['list_reply']['id'] ?? null;
            $listTitle = $message['interactive']['list_reply']['title'] ?? null;
            $askedText = $listTitle ?? $askedText;
        }

        return [
            'wa_message_id' => $message['id'] ?? null,
            'message_type' => $messageType,
            'asked_text' => $askedText,
            'button_id' => $buttonId,
            'button_title' => $buttonTitle,
            'list_id' => $listId,
            'list_title' => $listTitle,
        ];
    }

    private function extractOutboundMessageDetails(array $payload, $response = null): array
    {
        $messageType = isset($payload['type']) ? (string) $payload['type'] : 'unknown';
        $askedText = null;

        if ($messageType === 'text') {
            $askedText = $payload['text']['body'] ?? null;
        } elseif ($messageType === 'interactive') {
            $askedText = $payload['interactive']['body']['text'] ?? null;
            $interactiveType = $payload['interactive']['type'] ?? null;
            if ($interactiveType) {
                $messageType = 'interactive_' . $interactiveType;
            }
        } elseif ($messageType === 'template') {
            $templateName = $payload['template']['name'] ?? 'template';
            $askedText = 'Template: ' . $templateName;
        }

        return [
            'wa_message_id' => data_get($response ? $response->json() : [], 'messages.0.id'),
            'message_type' => $messageType,
            'asked_text' => $askedText,
        ];
    }

    private function upsertChatContact(string $number, $now): int
    {
        $number = trim($number);
        $existing = DB::table('wa_contacts')->select('id')->where('wa_number', $number)->first();

        if ($existing) {
            DB::table('wa_contacts')
                ->where('id', $existing->id)
                ->update([
                    'last_seen_at' => $now,
                    'updated_at' => $now,
                ]);

            return (int) $existing->id;
        }

        return (int) DB::table('wa_contacts')->insertGetId([
            'wa_number' => $number,
            'first_seen_at' => $now,
            'last_seen_at' => $now,
            'total_inbound' => 0,
            'total_outbound' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function resolveChatSessionId(int $contactId, string $startedBy, ?string $firstMessageText, $now): int
    {
        $openSession = DB::table('wa_chat_sessions')
            ->select('id', 'last_activity_at')
            ->where('contact_id', $contactId)
            ->where('status', 'open')
            ->orderByDesc('id')
            ->first();

        $idleCutoff = Carbon::parse($now)->subMinutes(self::CHAT_SESSION_IDLE_MINUTES);

        if ($openSession && Carbon::parse($openSession->last_activity_at)->gte($idleCutoff)) {
            DB::table('wa_chat_sessions')
                ->where('id', $openSession->id)
                ->update([
                    'last_activity_at' => $now,
                    'updated_at' => $now,
                ]);

            return (int) $openSession->id;
        }

        DB::table('wa_chat_sessions')
            ->where('contact_id', $contactId)
            ->where('status', 'open')
            ->update([
                'status' => 'closed',
                'ended_at' => $now,
                'updated_at' => $now,
            ]);

        return (int) DB::table('wa_chat_sessions')->insertGetId([
            'contact_id' => $contactId,
            'started_by' => $startedBy,
            'started_at' => $now,
            'last_activity_at' => $now,
            'ended_at' => null,
            'status' => 'open',
            'first_message_text' => $firstMessageText,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}

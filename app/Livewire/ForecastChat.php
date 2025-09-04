<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Services\OpenAIService;
use Illuminate\Support\Facades\DB;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ForecastChat extends Component
{
    public string $input = '';
    public array $messages = [];
    public int|string|null $branchId = 'all';
    public int $topN = 50; // limit products for token safety
    public Collection $branches;
    public bool $isProcessing = false;

    public function mount(): void
    {
        $this->branches = Branch::where('company_id', session('company_id'))->get();
        // dd($this->branches);
        $this->messages[] = [
            'role' => 'assistant',
            'content' => 'Hello! Ask me about sales forecasts and reorder suggestions. You can ask about specific dates like "yesterday", "last week", "september", or custom periods like "last 15 days", "past month", "recent trends". Just tell me what time period you want to analyze!'
        ];
    }

    private function parseMarkdown($text)
    {
        $converter = new GithubFlavoredMarkdownConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'renderer' => [
                'soft_break' => "\n",
            ],
        ]);

        return $converter->convert($text)->getContent();
    }

    private function parseDateFromUserInput(string $userInput): array
    {
        $userInput = strtolower(trim($userInput));
        
        // Urdu helpers (normalize common Urdu transliterations without diacritics)
        $normalized = $userInput;
        
        // Normalize common English number words to digits for day ranges (e.g., "last two days")
        // This is a lightweight pass intended to improve parsing for small counts (<=30)
        $wordToNumber = [
            'zero' => '0',
            'one' => '1',
            'two' => '2',
            'three' => '3',
            'four' => '4',
            'five' => '5',
            'six' => '6',
            'seven' => '7',
            'eight' => '8',
            'nine' => '9',
            'ten' => '10',
            'eleven' => '11',
            'twelve' => '12',
            'thirteen' => '13',
            'fourteen' => '14',
            'fifteen' => '15',
            'sixteen' => '16',
            'seventeen' => '17',
            'eighteen' => '18',
            'nineteen' => '19',
            'twenty' => '20',
            'thirty' => '30',
            'couple' => '2',
            'few' => '3',
        ];
        $pattern = '/\\b(' . implode('|', array_map('preg_quote', array_keys($wordToNumber))) . ')\\b/';
        $normalized = preg_replace_callback($pattern, function ($m) use ($wordToNumber) {
            return $wordToNumber[$m[1]] ?? $m[0];
        }, $normalized);
        
        // Today / Aaj
        if (preg_match('/\\b(today|aaj)\\b/u', $normalized)) {
            $startDate = now()->startOfDay();
            $endDate = now()->endOfDay();
            return ['start' => $startDate, 'end' => $endDate, 'type' => 'specific'];
        }
        
        // Yesterday / Kal (assume yesterday)
        if (preg_match('/\\b(yesterday|kal)\\b/u', $normalized)) {
            $startDate = now()->subDay()->startOfDay();
            $endDate = now()->subDay()->endOfDay();
            return ['start' => $startDate, 'end' => $endDate, 'type' => 'specific'];
        }
        
        // Last week / Pichlay hafte
        if (preg_match('/\\b(last\\s+week|pichlay\\s+haft(e|ay))\\b/u', $normalized)) {
            $startDate = now()->subWeek()->startOfWeek();
            $endDate = now()->subWeek()->endOfWeek();
            return ['start' => $startDate, 'end' => $endDate, 'type' => 'specific'];
        }
        
        // This week / Is hafte
        if (preg_match('/\\b(this\\s+week|is\\s+haft(e|ay))\\b/u', $normalized)) {
            $startDate = now()->startOfWeek();
            $endDate = now()->endOfWeek();
            return ['start' => $startDate, 'end' => $endDate, 'type' => 'specific'];
        }
        
        // Last month / Pichlay mahine
        if (preg_match('/\\b(last\\s+month|pichlay\\s+mahin(e|ay))\\b/u', $normalized)) {
            $startDate = now()->subMonth()->startOfMonth();
            $endDate = now()->subMonth()->endOfMonth();
            return ['start' => $startDate, 'end' => $endDate, 'type' => 'specific'];
        }
        
        // This month / Is mahine
        if (preg_match('/\\b(this\\s+month|is\\s+mahin(e|ay))\\b/u', $normalized)) {
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
            return ['start' => $startDate, 'end' => $endDate, 'type' => 'specific'];
        }
        
        // Last X days / Past X days / Recent X days / Aakhri X din
        if (preg_match('/\b(last|past|recent|previous|aakhri)\s+(?:the\s+)?(\d+)\s+(?:of\s+)?din(s)?\b/u', $normalized, $m)) {
            $days = (int)$m[2];
            if ($days > 0 && $days <= 365) {
                $startDate = now()->subDays($days)->startOfDay();
                $endDate = now()->subDay()->endOfDay();
                return ['start' => $startDate, 'end' => $endDate, 'type' => 'range'];
            }
        }
        if (preg_match('/\b(last|past|recent|previous)\s+(?:the\s+)?(\d+)\s+(?:of\s+)?days?\b/i', $normalized, $matches)) {
            $days = (int)$matches[2];
            if ($days > 0 && $days <= 365) {
                $startDate = now()->subDays($days)->startOfDay();
                $endDate = now()->subDay()->endOfDay();
                return ['start' => $startDate, 'end' => $endDate, 'type' => 'range'];
            }
        }
        
        // X days ago / X din pehlay
        if (preg_match('/\\b(\\d+)\\s+din\\s+pehl(e|ay)\\b/u', $normalized, $m)) {
            $days = (int)$m[1];
            if ($days > 0 && $days <= 365) {
                $startDate = now()->subDays($days)->startOfDay();
                $endDate = now()->subDays($days)->endOfDay();
                return ['start' => $startDate, 'end' => $endDate, 'type' => 'specific'];
            }
        }
        if (preg_match('/\\b(\\d+)\\s+days?\\s+ago\\b/i', $normalized, $matches)) {
            $days = (int)$matches[1];
            if ($days > 0 && $days <= 365) {
                $startDate = now()->subDays($days)->startOfDay();
                $endDate = now()->subDays($days)->endOfDay();
                return ['start' => $startDate, 'end' => $endDate, 'type' => 'specific'];
            }
        }
        
        // Existing English formats below ...
        // Check for specific date patterns (e.g., "2024-01-15")
        if (preg_match('/(\\d{4}-\\d{2}-\\d{2})/', $normalized, $matches)) {
            try {
                $date = Carbon::parse($matches[1]);
                $startDate = $date->startOfDay();
                $endDate = $date->endOfDay();
                return ['start' => $startDate, 'end' => $endDate, 'type' => 'specific'];
            } catch (\Exception $e) {
                return ['start' => null, 'end' => null, 'type' => 'range'];
            }
        }
        
        // Natural formats like "31 sept" remain unchanged...
        if (preg_match('/(\\d{1,2})\\s+(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec|january|february|march|april|may|june|july|august|september|october|november|december)/i', $normalized, $matches)) {
            $day = (int)$matches[1];
            $month = strtolower($matches[2]);
            $monthMap = [
                'jan' => 1, 'january' => 1,
                'feb' => 2, 'february' => 2,
                'mar' => 3, 'march' => 3,
                'apr' => 4, 'april' => 4,
                'may' => 5,
                'jun' => 6, 'june' => 6,
                'jul' => 7, 'july' => 7,
                'aug' => 8, 'august' => 8,
                'sep' => 9, 'september' => 9,
                'oct' => 10, 'october' => 10,
                'nov' => 11, 'november' => 11,
                'dec' => 12, 'december' => 12,
            ];
            if (isset($monthMap[$month])) {
                $monthNum = $monthMap[$month];
                $currentYear = now()->year;
                try {
                    $date = Carbon::create($currentYear, $monthNum, $day);
                    if ($date->month === $monthNum && $date->day === $day) {
                        $startDate = $date->startOfDay();
                        $endDate = $date->endOfDay();
                        return ['start' => $startDate, 'end' => $endDate, 'type' => 'specific'];
                    } else {
                        return ['start' => null, 'end' => null, 'type' => 'range', 'error' => "Invalid date: {$day} {$month}"];
                    }
                } catch (\Exception $e) {
                    return ['start' => null, 'end' => null, 'type' => 'range', 'error' => "Invalid date: {$day} {$month}"];
                }
            }
        }
        
        // Month-only patterns (English) remain unchanged...
        if (preg_match('/(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec|january|february|march|april|may|june|july|august|september|october|november|december)/i', $normalized, $matches)) {
            $month = strtolower($matches[1]);
            $monthMap = [
                'jan' => 1, 'january' => 1,
                'feb' => 2, 'february' => 2,
                'mar' => 3, 'march' => 3,
                'apr' => 4, 'april' => 4,
                'may' => 5,
                'jun' => 6, 'june' => 6,
                'jul' => 7, 'july' => 7,
                'aug' => 8, 'august' => 8,
                'sep' => 9, 'september' => 9,
                'oct' => 10, 'october' => 10,
                'nov' => 11, 'november' => 11,
                'dec' => 12, 'december' => 12,
            ];
            if (isset($monthMap[$month])) {
                $monthNum = $monthMap[$month];
                $currentYear = now()->year;
                $currentMonth = now()->month;
                $year = $monthNum <= $currentMonth ? $currentYear : $currentYear - 1;
                $startDate = Carbon::create($year, $monthNum, 1)->startOfMonth();
                $endDate = Carbon::create($year, $monthNum, 1)->endOfMonth();
                return ['start' => $startDate, 'end' => $endDate, 'type' => 'specific'];
            }
        }
        
        // Month + context patterns (English) remain unchanged...
        if (preg_match('/(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec|january|february|march|april|may|june|july|august|september|october|november|december)\\s+(sales|most\\s+selling|items|trends)/i', $normalized, $matches)) {
            $month = strtolower($matches[1]);
            $monthMap = [
                'jan' => 1, 'january' => 1,
                'feb' => 2, 'february' => 2,
                'mar' => 3, 'march' => 3,
                'apr' => 4, 'april' => 4,
                'may' => 5,
                'jun' => 6, 'june' => 6,
                'jul' => 7, 'july' => 7,
                'aug' => 8, 'august' => 8,
                'sep' => 9, 'september' => 9,
                'oct' => 10, 'october' => 10,
                'nov' => 11, 'november' => 11,
                'dec' => 12, 'december' => 12,
            ];
            if (isset($monthMap[$month])) {
                $monthNum = $monthMap[$month];
                $currentYear = now()->year;
                $currentMonth = now()->month;
                $year = $monthNum <= $currentMonth ? $currentYear : $currentYear - 1;
                $startDate = Carbon::create($year, $monthNum, 1)->startOfMonth();
                $endDate = Carbon::create($year, $monthNum, 1)->endOfMonth();
                return ['start' => $startDate, 'end' => $endDate, 'type' => 'specific'];
            }
        }
        
        // Recent keywords default (English)
        if (preg_match('/(recent|latest|recently)/i', $normalized)) {
            $startDate = now()->subDays(7)->startOfDay();
            $endDate = now()->subDay()->endOfDay();
            return ['start' => $startDate, 'end' => $endDate, 'type' => 'range'];
        }
        
        // Default to the last 30 days if no specific pattern is found
        return ['start' => null, 'end' => null, 'type' => 'range'];
    }

    private function debugDatabaseInfo(): array
    {
        $debug = [];
        
        // Check recent sales data
        $recentSales = DB::table('sales_receipts as sr')
            ->select('sr.date', 'sr.branch')
            ->orderBy('sr.date', 'desc')
            ->limit(5)
            ->get();
        
        $debug['recent_sales'] = $recentSales->toArray();
        
        // Check total sales count
        $totalSales = DB::table('sales_receipts')->count();
        $debug['total_sales_count'] = $totalSales;
        
        // Check if there are any sales for yesterday
        $yesterday = now()->subDay()->format('Y-m-d');
        $yesterdaySales = DB::table('sales_receipts')
            ->whereDate('date', $yesterday)
            ->count();
        $debug['yesterday_sales_count'] = $yesterdaySales;
        
        // Check what dates are actually available
        $availableDates = DB::table('sales_receipts')
            ->selectRaw('DATE(date) as date_only, COUNT(*) as count')
            ->groupBy(DB::raw('DATE(date)'))
            ->orderBy('date_only', 'desc')
            ->limit(10)
            ->get();
        $debug['available_dates'] = $availableDates->toArray();
        
        // Check the date column type and sample values
        try {
            $sampleDates = DB::table('sales_receipts')
                ->select('date')
                ->limit(5)
                ->get();
            $debug['sample_dates'] = $sampleDates->toArray();
        } catch (\Exception $e) {
            $debug['sample_dates_error'] = $e->getMessage();
        }
        
        // Check available branches
        $branches = DB::table('branch')->pluck('branch_id')->toArray();
        $debug['available_branches'] = $branches;
        
        // Check current session company_id
        $debug['session_company_id'] = session('company_id');
        
        return $debug;
    }

    private function formatDebugOutput(array $debugQueries, array $debugData): string
    {
        $output = "🔍 **DEBUG INFORMATION**\n\n";
        
        $output .= "**SQL Queries Executed:**\n";
        foreach ($debugQueries as $query) {
            $output .= "• **{$query['step']}**:\n";
            $output .= "  - SQL: `{$query['sql']}`\n";
            $output .= "  - Bindings: " . json_encode($query['bindings']) . "\n";
            if (isset($query['date_range'])) {
                $output .= "  - Date Range: {$query['date_range']}\n";
            }
            if (isset($query['branch_filter'])) {
                $output .= "  - Branch Filter: {$query['branch_filter']}\n";
            }
            $output .= "\n";
        }
        
        $output .= "**Data Flow:**\n";
        foreach ($debugData as $data) {
            $output .= "• **{$data['step']}**:\n";
            foreach ($data as $key => $value) {
                if ($key !== 'step') {
                    if (is_array($value)) {
                        $output .= "  - {$key}: " . json_encode($value) . "\n";
                    } else {
                        $output .= "  - {$key}: {$value}\n";
                    }
                }
            }
            $output .= "\n";
        }
        
        return $output;
    }

    private function formatSingleDayReport(array $salesSummary, array $stockMap, array $productMap, string $label): string
    {
        $lines = [];
        $lines[] = "### Sales summary for {$label}";
        $lines[] = "";
        $lines[] = "| Product | Sales | Stock | Need 7d | Need 30d |";
        $lines[] = "|---|---:|---:|---:|---:|";
        foreach ($salesSummary as $productId => $totalQty) {
            $stock = $stockMap[$productId] ?? 0;
            $salesQty = $totalQty; // single day sales
            $need7 = max(0, (int)ceil($salesQty * 7 - $stock));
            $need30 = max(0, (int)ceil($salesQty * 30 - $stock));
            $name = $productMap[$productId] ?? (string)$productId;
            $lines[] = "| {$name} | {$salesQty} | {$stock} | {$need7} | {$need30} |";
        }
        if (count($lines) === 4) {
            $lines[] = "| — | 0 | 0 | 0 | 0 |";
        }
        $lines[] = "";
        $lines[] = "- Top movers are products with highest Sales.";
        $lines[] = "- Low stock risks are items where Need 7d/30d is positive.";
        return implode("\n", $lines);
    }

    public function send(OpenAIService $ai)
    {
        // Set processing to true immediately when send is called
        $this->isProcessing = true;
        
        $userText = trim($this->input);
        if ($userText === '') {
            $this->isProcessing = false;
            return;
        }

        // Add user message to chat
        $this->messages[] = ['role' => 'user', 'content' => $userText];
        $this->input = '';

        // Parse date from user input
        $dateInfo = $this->parseDateFromUserInput($userText);
        Log::info("dateInfo: ".json_encode($dateInfo));
        // Get sales and stock data
        [$salesSummary, $stockMap, $productMap, $dateContext, $debugQueries, $debugData] = $this->summaries($dateInfo);
        Log::info("salesSummary: ".json_encode($salesSummary));
        Log::info("stockMap: ".json_encode($stockMap));
        Log::info("productMap: ".json_encode($productMap));
        Log::info("dateContext: ".json_encode($dateContext));
        Log::info("debugQueries: ".json_encode($debugQueries));
        Log::info("debugData: ".json_encode($debugData));
        
        // If this is a specific one-day request (today/yesterday or exact date) and we have data, respond deterministically without AI
        if (($dateInfo['type'] ?? null) === 'specific' && !empty($salesSummary)) {
            // Try to extract yyyy-mm-dd label from dateContext
            $label = $dateContext;
            if (preg_match('/(\d{4}-\d{2}-\d{2})/', $dateContext, $m)) {
                $label = $m[1];
            }
            $answer = $this->formatSingleDayReport($salesSummary, $stockMap, $productMap, $label);
            $answer = $this->parseMarkdown($answer);
            $this->messages[] = ['role' => 'assistant', 'content' => $answer];
            $this->isProcessing = false;
            return;
        }

        // Add debug info if no sales found
        $debugInfo = [];
        if (empty($salesSummary)) {
            $debugInfo = $this->debugDatabaseInfo();
        }

        $context = [
            'filters' => [
                'branch_id' => $this->branchId,
                'date_context' => $dateContext,
            ],
            'sales_summary' => $salesSummary,
            'stock_levels' => $stockMap,
            'product_names' => $productMap,
            'debug_info' => $debugInfo,
            'debug_queries' => $debugQueries,
            'debug_data' => $debugData,
        ];

        $messages = [
            [
                'role' => 'system',
                'content' => implode("\n", [
                    'You are an ERP assistant that provides inventory reorder recommendations.',
                    'Authoritative Data Policy:',
                    '- STRICTLY use the provided Context JSON as the sole source of truth.',
                    '- NEVER reference training data, external knowledge, or claim data cutoffs.',
                    '- NEVER say a date is invalid if Context JSON contains results for it.',
                    'Response Rules:',
                    '- If sales_summary has entries, generate the table and insights from Context JSON without disclaimers.',
                    '- Only state that no data is available if sales_summary is empty. If empty, use debug info to suggest nearest available period (e.g., fallback_days) and ask a follow-up.',
                    '- Calculate next 7-day and 30-day needs.',
                    '- Formula: avg_daily = total_qty / days; need_7d = max(0, ceil(avg_daily*7 - stock)); need_30d = max(0, ceil(avg_daily*30 - stock)).',
                    '- Only include products where need_7d > 0 or need_30d > 0.',
                    '- Present in a neat Markdown table with header row and separators: | Product | Avg/Day | Stock | Need 7d | Need 30d |',
                    '- Do not duplicate products; aggregate by product id.',
                    '- Clamp obviously unrealistic values (e.g., stock > 1e6) to a readable shortened format and call them out.',
                    '- Add bullet-point insights for top movers and low stock risks.',
                    '- If analyzing a specific date or short period, tailor insights to that time frame.',
                    '- For invalid user-entered dates (e.g., "31 September"), politely explain and use the chosen fallback, but only when sales_summary is empty.',
                    '- IMPORTANT: If the user asks for "debug" or "show me the queries", display concise debug details from debug_queries and debug_data.',
                ]),
            ],
            ['role' => 'system', 'content' => 'Context JSON: ' . json_encode($context)],
            ...$this->messages
        ];

        try {
            $answer = $ai->ask($messages);
            $answer = $this->parseMarkdown($answer);
        } catch (\Throwable $e) {
            $answer = "Error contacting AI service: " . e($e->getMessage());
        }

        $this->messages[] = ['role' => 'assistant', 'content' => $answer];
        $this->isProcessing = false;
    }

    protected function summaries(array $dateInfo = null): array
    {
        $dateContext = '';
        $debugQueries = [];
        $debugData = [];
        $noDateProvided = false;
        
        if ($dateInfo && in_array($dateInfo['type'], ['specific', 'range'], true)) {
            // Check if there's an error in date parsing
            if (isset($dateInfo['error'])) {
                $dateContext = $dateInfo['error'] . " - Please specify a valid date or period (e.g., 'yesterday', 'last 15 days').";
                // Do NOT run default queries when input is invalid
                $salesRows = collect();
            } else {
                // Use specific date range
                $startDate = $dateInfo['start'];
                $endDate = $dateInfo['end'];
                if ($dateInfo['type'] === 'specific') {
                    $dateContext = "Analyzing sales for " . (is_string($startDate) ? $startDate : $startDate->toDateString());
                } else {
                    $dateContext = "Analyzing sales from " . (is_string($startDate) ? $startDate : $startDate->toDateString()) . " to " . (is_string($endDate) ? $endDate : $endDate->toDateString());
                }
                
                // Build query for either single date or a date range
                $salesQuery = DB::table('sales_receipt_details as s')
                    ->join('sales_receipts as sr', 's.receipt_id', '=', 'sr.id')
                    ->selectRaw('s.item_code, SUM(s.total_qty) as total_qty')
                    ->groupBy('s.item_code')
                    ->orderByDesc(DB::raw('SUM(s.total_qty)'));
                
                if ($dateInfo['type'] === 'specific') {
                    $salesQuery->whereDate('sr.date', is_string($startDate) ? $startDate : $startDate->toDateString());
                } else {
                    $salesQuery->whereBetween('sr.date', [
                        is_string($startDate) ? $startDate : $startDate->toDateTimeString(),
                        is_string($endDate) ? $endDate : $endDate->toDateTimeString(),
                    ]);
                }
                
                if (!empty($this->branchId) && $this->branchId != 'all') {
                    $salesQuery->where('sr.branch', $this->branchId);
                } else {
                    $salesQuery->whereIn('sr.branch', $this->branches->pluck('branch_id')->toArray());
                }
                
                // Debug: Log the specific date query
                $debugQueries[] = [
                    'step' => $dateInfo['type'] === 'specific' ? 'specific_date_query' : 'range_query',
                    'sql' => $salesQuery->toSql(),
                    'bindings' => $salesQuery->getBindings(),
                    'date_range' => $dateInfo['type'] === 'specific'
                        ? ("On: " . (is_string($startDate) ? $startDate : $startDate->toDateString()))
                        : ("From: " . (is_string($startDate) ? $startDate : $startDate->toDateString()) . " To: " . (is_string($endDate) ? $endDate : $endDate->toDateString())),
                    'branch_filter' => $this->branchId
                ];
                
                $salesRows = $salesQuery->limit($this->topN)->get();
                
                // Debug: Log the results
                $debugData[] = [
                    'step' => $dateInfo['type'] === 'specific' ? 'specific_date_results' : 'range_results',
                    'rows_count' => $salesRows->count(),
                    'sample_data' => $salesRows->take(3)->toArray()
                ];
            }
        } else {
            // No specific date provided: do not run default 30d query
            $noDateProvided = true;
            $dateContext = "No date provided. Please specify a period (e.g., 'yesterday', 'today', 'last 15 days', 'this week', 'this month').";
            $salesRows = collect();
        }

        // Do not run emergency fallback when no specific date provided
        if (!$noDateProvided && (empty($salesRows) || $salesRows->isEmpty())) {
            $emergencyDays = 30;
            $emergencySince = now()->subDays($emergencyDays)->startOfDay();
            $dateContext = "No results for selected date - Showing last {$emergencyDays} days (if any data exists)";
            
            $salesQuery = DB::table('sales_receipt_details as s')
                ->join('sales_receipts as sr', 's.receipt_id', '=', 'sr.id')
                ->selectRaw('s.item_code, SUM(s.total_qty) as total_qty')
                ->where('sr.date', '>=', $emergencySince)
                ->groupBy('s.item_code')
                ->orderByDesc(DB::raw('SUM(s.total_qty)'));
            
            if (!empty($this->branchId) && $this->branchId != 'all') {
                $salesQuery->where('sr.branch', $this->branchId);
            } else {
                $salesQuery->whereIn('sr.branch', $this->branches->pluck('branch_id')->toArray());
            }
            
            // Debug: Log the emergency query
            $debugQueries[] = [
                'step' => 'emergency_query',
                'sql' => $salesQuery->toSql(),
                'bindings' => $salesQuery->getBindings(),
                'date_range' => "Since: " . $emergencySince,
                'branch_filter' => $this->branchId
            ];
            
            $salesRows = $salesQuery->limit($this->topN)->get();
            
            // Debug: Log the emergency results
            $debugData[] = [
                'step' => 'emergency_results',
                'rows_count' => $salesRows->count(),
                'sample_data' => $salesRows->take(3)->toArray()
            ];
        }

        $productIds = $salesRows->pluck('item_code')->unique()->values();
        
        // Debug: Log product IDs found
        $debugData[] = [
            'step' => 'product_ids_extracted',
            'product_ids_count' => $productIds->count(),
            'product_ids_sample' => $productIds->take(5)->toArray()
        ];
        
        // If no products found: do NOT fetch fallback products when no date provided
        if ($productIds->isEmpty()) {
            if ($noDateProvided) {
                $stockRows = collect();
                $productRows = collect();
            } else {
                $productIds = DB::table('inventory_general as p')->select('p.id')->limit(10)->pluck('id');
                // Debug: Log fallback product query
                $debugData[] = [
                    'step' => 'fallback_products',
                    'fallback_product_count' => $productIds->count(),
                    'fallback_product_ids' => $productIds->toArray()
                ];
            }
        }
        
        $stockMap = [];
        $productMap = [];
        
        if (!$productIds->isEmpty()) {
            $stockQuery = DB::table('inventory_stock as i')
                ->selectRaw('i.product_id, SUM(i.balance) as stock_level')
                ->whereIn('i.product_id', $productIds)
                ->where('i.status_id', 1);

            // If a branch is selected, use that branch's stock only
            if (!empty($this->branchId) && $this->branchId != 'all') {
                $stockQuery->where('i.branch_id', $this->branchId);
            } else {
                $stockQuery->whereIn('i.branch_id', $this->branches->pluck('branch_id')->toArray());
            }
            
            // Debug: Log the stock query
            $debugQueries[] = [
                'step' => 'stock_query',
                'sql' => $stockQuery->toSql(),
                'bindings' => $stockQuery->getBindings(),
                'product_ids' => $productIds->toArray(),
                'branch_filter' => $this->branchId
            ];
            
            $stockRows = $stockQuery->groupBy('i.product_id')->get();
            
            // Debug: Log stock results
            $debugData[] = [
                'step' => 'stock_results',
                'stock_rows_count' => $stockRows->count(),
                'stock_sample' => $stockRows->take(3)->toArray()
            ];

            $productRows = DB::table('inventory_general as p')
                ->select('p.id', 'p.product_name')
                ->whereIn('p.id', $productIds)
                ->get();
                
            // Debug: Log product results
            $debugData[] = [
                'step' => 'product_results',
                'product_rows_count' => $productRows->count(),
                'product_sample' => $productRows->take(3)->toArray()
            ];

            foreach ($stockRows as $r) {
                $stockMap[(int)$r->product_id] = (int)$r->stock_level;
            }
            foreach ($productRows as $r) {
                $productMap[(int)$r->id] = $r->product_name;
            }
        }

        $salesSummary = [];
        foreach ($salesRows as $r) {
            $salesSummary[(int)$r->item_code] = (int)$r->total_qty;
        }
        
        // Debug: Log final summary
        $debugData[] = [
            'step' => 'final_summary',
            'sales_summary_count' => count($salesSummary),
            'stock_map_count' => count($stockMap),
            'product_map_count' => count($productMap),
            'sales_summary_sample' => array_slice($salesSummary, 0, 3, true)
        ];

        return [$salesSummary, $stockMap, $productMap, $dateContext, $debugQueries, $debugData];
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.forecast-chat');
    }
}

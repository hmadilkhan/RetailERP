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

class ForecastChat extends Component
{
    public string $input = '';
    public array $messages = [];
    public int|string|null $branchId = 'all';
    public string $dateRange = '30d'; // 7d | 30d | 90d | custom
    public int $topN = 50; // limit products for token safety
    public Collection $branches;
    public bool $isProcessing = false;
    public ?string $customStartDate = null;
    public ?string $customEndDate = null;

    public function mount(): void
    {
        $this->branches = Branch::where('company_id', session('company_id'))->get();
        // dd($this->branches);
        $this->messages[] = [
            'role' => 'assistant',
            'content' => 'Hello! Ask me about sales forecasts and reorder suggestions. You can ask about specific dates like "yesterday", "last week", or specific date ranges.'
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
        
        // Check for specific date patterns
        if (preg_match('/(yesterday|today)/', $userInput)) {
            if (strpos($userInput, 'yesterday') !== false) {
                $startDate = now()->subDay()->startOfDay();
                $endDate = now()->subDay()->endOfDay();
                return ['start' => $startDate, 'end' => $endDate, 'type' => 'specific'];
            } elseif (strpos($userInput, 'today') !== false) {
                $startDate = now()->startOfDay();
                $endDate = now()->endOfDay();
                return ['start' => $startDate, 'end' => $endDate, 'type' => 'specific'];
            }
        }
        
        // Check for "last week" pattern
        if (preg_match('/last\s+week/', $userInput)) {
            $startDate = now()->subWeek()->startOfWeek();
            $endDate = now()->subWeek()->endOfWeek();
            return ['start' => $startDate, 'endDate' => $endDate, 'type' => 'specific'];
        }
        
        // Check for "last month" pattern
        if (preg_match('/last\s+month/', $userInput)) {
            $startDate = now()->subMonth()->startOfMonth();
            $endDate = now()->subMonth()->endOfMonth();
            return ['start' => $startDate, 'end' => $endDate, 'type' => 'specific'];
        }
        
        // Check for specific date patterns (e.g., "2024-01-15" or "January 15")
        if (preg_match('/(\d{4}-\d{2}-\d{2})/', $userInput, $matches)) {
            try {
                $date = Carbon::parse($matches[1]);
                $startDate = $date->startOfDay();
                $endDate = $date->endOfDay();
                return ['start' => $startDate, 'end' => $endDate, 'type' => 'specific'];
            } catch (\Exception $e) {
                // Invalid date format, fall back to range
                return ['start' => null, 'end' => null, 'type' => 'range'];
            }
        }
        
        // Check for natural date formats like "31 sept", "sept 31", "31 September", etc.
        if (preg_match('/(\d{1,2})\s+(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec|january|february|march|april|may|june|july|august|september|october|november|december)/i', $userInput, $matches)) {
            $day = (int)$matches[1];
            $month = strtolower($matches[2]);
            
            // Map month names to numbers
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
                    // Try to create the date
                    $date = Carbon::create($currentYear, $monthNum, $day);
                    
                    // Check if the date is valid (e.g., Feb 30 doesn't exist)
                    if ($date->month === $monthNum && $date->day === $day) {
                        $startDate = $date->startOfDay();
                        $endDate = $date->endOfDay();
                        return ['start' => $startDate, 'end' => $endDate, 'type' => 'specific'];
                    } else {
                        // Invalid date (like Sept 31), fall back to range
                        return ['start' => null, 'end' => null, 'type' => 'range', 'error' => "Invalid date: {$day} {$month}"];
                    }
                } catch (\Exception $e) {
                    // Invalid date, fall back to range
                    return ['start' => null, 'end' => null, 'type' => 'range', 'error' => "Invalid date: {$day} {$month}"];
                }
            }
        }
        
        // Check for month-only patterns like "september", "sept", etc.
        if (preg_match('/(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec|january|february|march|april|may|june|july|august|september|october|november|december)/i', $userInput, $matches)) {
            $month = strtolower($matches[1]);
            
            // Map month names to numbers
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
                
                // If asking about current month, use current month
                // If asking about past months, use last occurrence
                $currentMonth = now()->month;
                if ($monthNum <= $currentMonth) {
                    $year = $currentYear;
                } else {
                    $year = $currentYear - 1;
                }
                
                $startDate = Carbon::create($year, $monthNum, 1)->startOfMonth();
                $endDate = Carbon::create($year, $monthNum, 1)->endOfMonth();
                return ['start' => $startDate, 'end' => $endDate, 'type' => 'specific'];
            }
        }
        
        // Check for month + "sales" or "most selling" patterns
        if (preg_match('/(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec|january|february|march|april|may|june|july|august|september|october|november|december)\s+(sales|most\s+selling|items|trends)/i', $userInput, $matches)) {
            $month = strtolower($matches[1]);
            
            // Map month names to numbers
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
                
                // If asking about current month, use current month
                // If asking about past months, use last occurrence
                $currentMonth = now()->month;
                if ($monthNum <= $currentMonth) {
                    $year = $currentYear;
                } else {
                    $year = $currentYear - 1;
                }
                
                $startDate = Carbon::create($year, $monthNum, 1)->startOfMonth();
                $endDate = Carbon::create($year, $monthNum, 1)->endOfMonth();
                return ['start' => $startDate, 'end' => $endDate, 'type' => 'specific'];
            }
        }
        
        // Default to the selected date range
        return ['start' => null, 'end' => null, 'type' => 'range'];
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
        
        // Get sales and stock data
        [$salesSummary, $stockMap, $productMap, $dateContext] = $this->summaries($dateInfo);

        $context = [
            'filters' => [
                'branch_id' => $this->branchId,
                'date_range' => $this->dateRange,
                'date_context' => $dateContext,
            ],
            'sales_summary' => $salesSummary,
            'stock_levels' => $stockMap,
            'product_names' => $productMap,
        ];

        $messages = [
            [
                'role' => 'system',
                'content' => implode("\n", [
                    'You are an ERP assistant that provides inventory reorder recommendations.',
                    'Rules:',
                    '- Calculate next 7-day and 30-day needs.',
                    '- Formula: avg_daily = total_qty / days; need_7d = max(0, ceil(avg_daily*7 - stock)); need_30d = max(0, ceil(avg_daily*30 - stock)).',
                    '- Only include products where need_7d > 0 or need_30d > 0.',
                    '- Present in a neat Markdown table with header row and separators: | Product | Avg/Day | Stock | Need 7d | Need 30d |',
                    '- Do not duplicate products; aggregate by product id.',
                    '- Clamp obviously unrealistic values (e.g., stock > 1e6) to a readable shortened format and call them out.',
                    '- Add bullet-point insights for top movers and low stock risks.',
                    '- If analyzing a specific date or short period, provide insights about that specific time frame.',
                    '- If the user asks for an invalid date (like "31 September"), politely explain the issue and provide the analysis for the fallback period.',
                    '- Always be helpful and provide actionable insights even when date queries need adjustment.',
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
        
        if ($dateInfo && $dateInfo['type'] === 'specific') {
            // Check if there's an error in date parsing
            if (isset($dateInfo['error'])) {
                $dateContext = $dateInfo['error'] . " - Falling back to last 30 days analysis";
                
                // Fall back to predefined date range
                $days = 30;
                $since = now()->subDays($days)->startOfDay();
                
                $salesQuery = DB::table('sales_receipt_details as s')
                    ->join('sales_receipts as sr', 's.receipt_id', '=', 'sr.id')
                    ->selectRaw('s.item_code, SUM(s.total_qty) as total_qty')
                    ->where('sr.date', '>=', $since)
                    ->groupBy('s.item_code')
                    ->orderByDesc(DB::raw('SUM(s.total_qty)'));
            } else {
                // Use specific date range
                $startDate = $dateInfo['start'];
                $endDate = $dateInfo['end'];
                $dateContext = "Analyzing sales from " . $startDate->format('M d, Y') . " to " . $endDate->format('M d, Y');
                
                $salesQuery = DB::table('sales_receipt_details as s')
                    ->join('sales_receipts as sr', 's.receipt_id', '=', 'sr.id')
                    ->selectRaw('s.item_code, SUM(s.total_qty) as total_qty')
                    ->whereBetween('sr.date', [$startDate, $endDate])
                    ->groupBy('s.item_code')
                    ->orderByDesc(DB::raw('SUM(s.total_qty)'));
            }
        } else {
            // Use predefined date range
            $days = match ($this->dateRange) {
                '7d' => 7,
                '90d' => 90,
                default => 30,
            };

            $since = now()->subDays($days)->startOfDay();
            $dateContext = "Analyzing sales from last {$days} days";
            
            $salesQuery = DB::table('sales_receipt_details as s')
                ->join('sales_receipts as sr', 's.receipt_id', '=', 'sr.id')
                ->selectRaw('s.item_code, SUM(s.total_qty) as total_qty')
                ->where('sr.date', '>=', $since)
                ->groupBy('s.item_code')
                ->orderByDesc(DB::raw('SUM(s.total_qty)'));
        }

        if (!empty($this->branchId) && $this->branchId != 'all') {
            $salesQuery->where('sr.branch', $this->branchId);
        } else {
            $salesQuery->whereIn('sr.branch', $this->branches->pluck('branch_id')->toArray());
        }

        $salesRows = $salesQuery->limit($this->topN)->get();
        $productIds = $salesRows->pluck('item_code')->unique()->values();
        
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
        
        $stockRows = $stockQuery->groupBy('i.product_id')->get();

        $productRows = DB::table('inventory_general as p')
            ->select('p.id', 'p.product_name')
            ->whereIn('p.id', $productIds)
            ->get();

        $salesSummary = [];
        foreach ($salesRows as $r) {
            $salesSummary[(int)$r->item_code] = (int)$r->total_qty;
        }

        $stockMap = [];
        foreach ($stockRows as $r) {
            $stockMap[(int)$r->product_id] = (int)$r->stock_level;
        }

        $productMap = [];
        foreach ($productRows as $r) {
            $productMap[(int)$r->id] = $r->product_name;
        }

        return [$salesSummary, $stockMap, $productMap, $dateContext];
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.forecast-chat');
    }
}

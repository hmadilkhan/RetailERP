<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Services\OpenAIService;
use Illuminate\Support\Facades\DB;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Collection;

class ForecastChat extends Component
{
    public string $input = '';
    public array $messages = [];
    public int|string|null $branchId = 'all';
    public string $dateRange = '30d'; // 7d | 30d | 90d
    public int $topN = 50; // limit products for token safety
    public Collection $branches;
    public bool $isProcessing = false;

    public function mount(): void
    {
        $this->branches = Branch::where('company_id', session('company_id'))->get();
        // dd($this->branches);
        $this->messages[] = [
            'role' => 'assistant',
            'content' => 'Hello! Ask me about sales forecasts and reorder suggestions.'
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

        // Get sales and stock data
        [$salesSummary, $stockMap, $productMap] = $this->summaries();

        $context = [
            'filters' => [
                'branch_id' => $this->branchId,
                'date_range' => $this->dateRange,
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

    protected function summaries(): array
    {
        $days = match ($this->dateRange) {
            '7d' => 7,
            '90d' => 90,
            default => 30,
        };

        $since = now()->subDays($days)->startOfDay();

        $salesQuery = DB::table('sales_receipt_details as s')
            ->join('sales_receipts as sr', 's.receipt_id', '=', 'sr.id')
            ->selectRaw('s.item_code, SUM(s.total_qty) as total_qty')
            ->where('sr.date', '>=', $since)
            // ->where('s.mode', 'inventory-general')
            ->groupBy('s.item_code')
            ->orderByDesc(DB::raw('SUM(s.total_qty)'));

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

        return [$salesSummary, $stockMap, $productMap];
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.forecast-chat');
    }
}

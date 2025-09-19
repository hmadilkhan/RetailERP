<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SalesForecastService
{
    protected $openaiKey;
    protected $model;

    public function __construct()
    {
        $this->openaiKey = env('OPENAI_API_KEY');
        $this->model = config('services.openai.model', 'gpt-4o-mini');
    }

    public function processQuery(string $userText, array $context = []): array
    {
        $intent = $this->detectIntent($userText);
        $dateInfo = $this->parseDateFromText($userText);
        
        switch ($intent['type']) {
            case 'sales_forecast':
                return $this->generateSalesForecast($dateInfo, $context);
            case 'sales_report':
                return $this->generateSalesReport($dateInfo, $context);
            case 'deal_creation':
                return $this->suggestDeals($context);
            case 'sales_prediction':
                return $this->predictSales($dateInfo, $context);
            default:
                return $this->generateGeneralResponse($userText, $context);
        }
    }

    private function detectIntent(string $text): array
    {
        $text = mb_strtolower($text);
        
        // Sales report keywords (actual sales data) - HIGHEST PRIORITY
        if (preg_match('/\b(sales|kitna.*tha|kitni.*thi|show.*sales|yesterday.*sales|kal.*sales|last.*week.*sales|dikhao|batao|btao)\b/u', $text)) {
            return ['type' => 'sales_report', 'confidence' => 0.95];
        }
        
        // Deal/package creation
        if (preg_match('/\b(deal|package|combo|offer|discount|bundle|package.*banao|deal.*karo|create.*deal)\b/u', $text)) {
            return ['type' => 'deal_creation', 'confidence' => 0.9];
        }
        
        // Sales forecast keywords (future predictions)
        if (preg_match('/\b(forecast|predict|future|prediction|kitna.*hoga|kitni.*hogi|agla|next|coming)\b/u', $text)) {
            return ['type' => 'sales_forecast', 'confidence' => 0.85];
        }
        
        // Sales prediction for specific future days
        if (preg_match('/\b(tomorrow.*sales|agla.*din|next.*monday|predict.*today|hoga|hogi)\b/u', $text)) {
            return ['type' => 'sales_prediction', 'confidence' => 0.8];
        }
        
        // Default to sales report for any sales-related query
        return ['type' => 'sales_report', 'confidence' => 0.7];
    }

    public function parseDateFromText(string $text): array
    {
        $tz = 'Asia/Karachi';
        $now = Carbon::now($tz);
        $normalized = $this->normalizeText($text);
        
        // Debug log
        \Log::info('Date parsing - Original: ' . $text . ' | Normalized: ' . $normalized);
        
        // Today variations
        if (preg_match('/\b(today|aaj|aj)\b/u', $normalized)) {
            return [
                'type' => 'specific',
                'start' => $now->toDateString(),
                'end' => $now->toDateString(),
                'label' => 'Today'
            ];
        }
        
        // Tomorrow
        if (preg_match('/\b(tomorrow|kal|agla.*din)\b/u', $normalized)) {
            $tomorrow = $now->copy()->addDay();
            return [
                'type' => 'specific',
                'start' => $tomorrow->toDateString(),
                'end' => $tomorrow->toDateString(),
                'label' => 'Tomorrow'
            ];
        }
        
        // Yesterday
        if (preg_match('/\b(yesterday|kal|pichla.*din)\b/u', $normalized)) {
            $yesterday = $now->copy()->subDay();
            return [
                'type' => 'specific',
                'start' => $yesterday->toDateString(),
                'end' => $yesterday->toDateString(),
                'label' => 'Yesterday'
            ];
        }
        
        // Specific weekdays
        $weekdays = [
            'monday|peer|somwar' => 1,
            'tuesday|mangal' => 2,
            'wednesday|budh' => 3,
            'thursday|jumerat|brihaspati' => 4,
            'friday|juma|shukrwar' => 5,
            'saturday|hafta|shaniwar' => 6,
            'sunday|itwaar' => 0
        ];
        
        foreach ($weekdays as $pattern => $dayOfWeek) {
            if (preg_match("/\b($pattern)\b/u", $normalized)) {
                $targetDate = $this->getNextWeekday($now, $dayOfWeek);
                return [
                    'type' => 'specific',
                    'start' => $targetDate->toDateString(),
                    'end' => $targetDate->toDateString(),
                    'label' => ucfirst(explode('|', $pattern)[0])
                ];
            }
        }
        
        // Last N days - PRIORITY PATTERN (multiple variations)
        if (preg_match('/\b(last|pichlay|pichley|pichle)\s+(\d+)\s+(days?|din)\b/u', $normalized, $matches)) {
            $days = (int)$matches[2];
            \Log::info('Pattern 1 matched: ' . $days . ' days');
            if ($days > 0 && $days <= 365) {
                $start = $now->copy()->subDays($days);
                return [
                    'type' => 'range',
                    'start' => $start->toDateString(),
                    'end' => $now->copy()->subDay()->toDateString(),
                    'label' => "Last $days days"
                ];
            }
        }
        
        // Alternative pattern: N din ki/ka/ke/sales
        if (preg_match('/\b(\d+)\s+(din|days?)\s*(ki|ka|ke|sales?)?\b/u', $normalized, $matches)) {
            $days = (int)$matches[1];
            \Log::info('Pattern 2 matched: ' . $days . ' days');
            if ($days > 0 && $days <= 365) {
                $start = $now->copy()->subDays($days);
                return [
                    'type' => 'range',
                    'start' => $start->toDateString(),
                    'end' => $now->copy()->subDay()->toDateString(),
                    'label' => "Last $days days"
                ];
            }
        }
        
        // Pattern: N days (simple)
        if (preg_match('/\b(\d+)\s+(days?|din)\b/u', $normalized, $matches)) {
            $days = (int)$matches[1];
            \Log::info('Pattern 3 matched: ' . $days . ' days');
            if ($days > 0 && $days <= 365) {
                $start = $now->copy()->subDays($days);
                return [
                    'type' => 'range',
                    'start' => $start->toDateString(),
                    'end' => $now->copy()->subDay()->toDateString(),
                    'label' => "Last $days days"
                ];
            }
        }
        
        // This week
        if (preg_match('/\b(this.*week|is.*hafta)\b/u', $normalized)) {
            return [
                'type' => 'range',
                'start' => $now->copy()->startOfWeek()->toDateString(),
                'end' => $now->copy()->endOfWeek()->toDateString(),
                'label' => 'This week'
            ];
        }
        
        // Last week
        if (preg_match('/\b(last.*week|pichla.*hafta)\b/u', $normalized)) {
            $lastWeek = $now->copy()->subWeek();
            return [
                'type' => 'range',
                'start' => $lastWeek->startOfWeek()->toDateString(),
                'end' => $lastWeek->endOfWeek()->toDateString(),
                'label' => 'Last week'
            ];
        }
        
        // This month
        if (preg_match('/\b(this.*month|is.*maheena|is.*mahina)\b/u', $normalized)) {
            return [
                'type' => 'range',
                'start' => $now->copy()->startOfMonth()->toDateString(),
                'end' => $now->copy()->endOfMonth()->toDateString(),
                'label' => 'This month'
            ];
        }
        
        // Default to no date (let user specify)
        return [
            'type' => 'none',
            'start' => null,
            'end' => null,
            'label' => 'No specific date provided'
        ];
    }

    private function normalizeText(string $text): string
    {
        $text = mb_strtolower($text);
        
        // Roman Urdu to English mappings
        $romanUrduMap = [
            'mjhe' => 'me',
            'mujhe' => 'me',
            'btao' => 'tell',
            'batao' => 'tell',
            'dikhao' => 'show',
            'dhikha' => 'show',
            'dain' => '',
            'karo' => 'do',
            'banao' => 'make',
            'kitna' => 'how much',
            'kitni' => 'how much',
            'kahi' => '',
            'hoga' => 'will be',
            'hai' => 'is',
            'tha' => 'was',
            'pichlay' => 'last',
            'pichley' => 'last', 
            'pichle' => 'last',
            'pichla' => 'last',
            'agla' => 'next',
            'aaj' => 'today',
            'kal' => 'yesterday',
            'din' => 'days',
            'hafta' => 'week',
            'maheena' => 'month',
            'mahina' => 'month',
            'saal' => 'year',
            'is' => 'this',
            'us' => 'that',
            'ki' => '',
            'ka' => '',
            'ke' => '',
            'sales' => 'sales'
        ];
        
        foreach ($romanUrduMap as $urdu => $english) {
            $text = preg_replace("/\b$urdu\b/u", $english, $text);
        }
        
        // Clean up extra spaces
        $text = preg_replace('/\s+/', ' ', trim($text));
        
        return $text;
    }

    private function getNextWeekday(Carbon $date, int $targetDayOfWeek): Carbon
    {
        $current = $date->copy();
        $currentDayOfWeek = $current->dayOfWeek;
        
        if ($currentDayOfWeek <= $targetDayOfWeek) {
            return $current->next($targetDayOfWeek);
        } else {
            return $current->addWeek()->next($targetDayOfWeek);
        }
    }

    public function generateSalesForecast(array $dateInfo, array $context): array
    {
        $historicalData = $this->getHistoricalSalesData($dateInfo, $context);
        $trends = $this->analyzeTrends($historicalData);
        $forecast = $this->calculateForecast($trends, $dateInfo);
        
        return [
            'type' => 'forecast',
            'data' => $forecast,
            'trends' => $trends,
            'recommendations' => $this->generateRecommendations($forecast, $trends)
        ];
    }

    public function generateSalesReport(array $dateInfo, array $context): array
    {
        $salesData = $this->getSalesData($dateInfo, $context);
        $analysis = $this->analyzeSalesData($salesData);
        
        return [
            'type' => 'report',
            'data' => $salesData,
            'analysis' => $analysis,
            'insights' => $this->generateInsights($analysis),
            'date_info' => $dateInfo
        ];
    }

    public function suggestDeals(array $context): array
    {
        $slowMovingItems = $this->getSlowMovingItems($context);
        $popularCombos = $this->getPopularCombinations($context);
        
        $deals = [];
        
        // Bundle slow-moving with popular items
        foreach ($slowMovingItems as $slowItem) {
            foreach ($popularCombos as $combo) {
                $deals[] = [
                    'type' => 'bundle',
                    'items' => array_merge([$slowItem], $combo['items']),
                    'discount' => $this->calculateOptimalDiscount($slowItem, $combo),
                    'expected_impact' => $this->predictDealImpact($slowItem, $combo)
                ];
            }
        }
        
        return [
            'type' => 'deals',
            'suggestions' => array_slice($deals, 0, 5), // Top 5 deals
            'reasoning' => $this->explainDealLogic($deals)
        ];
    }

    public function predictSales(array $dateInfo, array $context): array
    {
        $historicalPattern = $this->getHistoricalPattern($dateInfo, $context);
        $seasonalFactors = $this->getSeasonalFactors($dateInfo);
        $trendFactors = $this->getTrendFactors($context);
        
        $prediction = $this->calculatePrediction($historicalPattern, $seasonalFactors, $trendFactors);
        
        return [
            'type' => 'prediction',
            'predicted_sales' => $prediction,
            'confidence' => $this->calculateConfidence($historicalPattern),
            'factors' => [
                'seasonal' => $seasonalFactors,
                'trend' => $trendFactors,
                'historical' => $historicalPattern
            ]
        ];
    }

    private function getSalesData(array $dateInfo, array $context): array
    {
        $query = DB::table('sales_receipt_details as srd')
            ->join('sales_receipts as sr', 'srd.receipt_id', '=', 'sr.id')
            ->join('inventory_general as ig', 'srd.item_code', '=', 'ig.id')
            ->select([
                'ig.product_name',
                'srd.item_code',
                DB::raw('SUM(srd.total_qty) as total_quantity'),
                DB::raw('SUM(srd.total_amount) as total_amount'),
                DB::raw('COUNT(DISTINCT sr.id) as transaction_count'),
                DB::raw('AVG(srd.total_qty) as avg_quantity_per_transaction')
            ])
            ->groupBy('srd.item_code', 'ig.product_name');

        if ($dateInfo['type'] === 'specific') {
            $query->whereDate('sr.date', $dateInfo['start']);
        } elseif ($dateInfo['type'] === 'range') {
            $query->whereBetween('sr.date', [
                $dateInfo['start'] . ' 00:00:00',
                $dateInfo['end'] . ' 23:59:59'
            ]);
        }

        if (!empty($context['branch_id']) && $context['branch_id'] !== 'all') {
            $query->where('sr.branch', $context['branch_id']);
        } elseif (!empty($context['branches'])) {
            $query->whereIn('sr.branch', $context['branches']);
        }
        Log::info('Sales data query: ' . $query->orderByDesc('total_amount')->limit(20)->toSql() . ' | Bindings: ' . implode(', ', $query->getBindings()));
        return $query->orderByDesc('total_amount')->limit(20)->get()->toArray();
    }

    private function getHistoricalSalesData(array $dateInfo, array $context): array
    {
        // Get data for the same period in previous weeks/months
        $periods = [];
        $startDate = Carbon::parse($dateInfo['start']);
        
        for ($i = 1; $i <= 4; $i++) {
            $periodStart = $startDate->copy()->subWeeks($i);
            $periodEnd = $periodStart->copy()->addDays(
                Carbon::parse($dateInfo['end'])->diffInDays($startDate)
            );
            
            $periodData = $this->getSalesData([
                'type' => 'range',
                'start' => $periodStart->toDateString(),
                'end' => $periodEnd->toDateString()
            ], $context);
            
            $periods[] = [
                'period' => "Week -$i",
                'start' => $periodStart->toDateString(),
                'end' => $periodEnd->toDateString(),
                'data' => $periodData
            ];
        }
        
        return $periods;
    }

    private function analyzeTrends(array $historicalData): array
    {
        $trends = [];
        
        foreach ($historicalData as $index => $period) {
            if ($index === 0) continue;
            
            $currentTotal = array_sum(array_column($period['data'], 'total_amount'));
            $previousTotal = array_sum(array_column($historicalData[$index - 1]['data'], 'total_amount'));
            
            $growthRate = $previousTotal > 0 ? (($currentTotal - $previousTotal) / $previousTotal) * 100 : 0;
            
            $trends[] = [
                'period' => $period['period'],
                'total_sales' => $currentTotal,
                'growth_rate' => round($growthRate, 2)
            ];
        }
        
        return $trends;
    }

    private function calculateForecast(array $trends, array $dateInfo): array
    {
        $avgGrowthRate = array_sum(array_column($trends, 'growth_rate')) / count($trends);
        $lastPeriodSales = end($trends)['total_sales'] ?? 0;
        
        $forecastedSales = $lastPeriodSales * (1 + ($avgGrowthRate / 100));
        
        return [
            'forecasted_amount' => round($forecastedSales, 2),
            'growth_rate' => round($avgGrowthRate, 2),
            'confidence_level' => $this->calculateConfidenceLevel($trends),
            'date_range' => $dateInfo['label']
        ];
    }

    private function calculateConfidenceLevel(array $trends): string
    {
        $growthRates = array_column($trends, 'growth_rate');
        $variance = $this->calculateVariance($growthRates);
        
        if ($variance < 5) return 'High';
        if ($variance < 15) return 'Medium';
        return 'Low';
    }

    private function calculateVariance(array $values): float
    {
        $mean = array_sum($values) / count($values);
        $squaredDiffs = array_map(function($value) use ($mean) {
            return pow($value - $mean, 2);
        }, $values);
        
        return sqrt(array_sum($squaredDiffs) / count($squaredDiffs));
    }

    public function generateAIResponse(array $data, string $userText, bool $isRomanUrdu = false): string
    {
        $systemPrompt = $this->buildSystemPrompt($isRomanUrdu);
        $contextPrompt = $this->buildContextPrompt($data);
        
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'system', 'content' => $contextPrompt],
            ['role' => 'user', 'content' => $userText]
        ];

        $response = Http::withToken($this->openaiKey)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => $messages,
                'temperature' => 0.3,
                'max_tokens' => 1500
            ]);

        return $response->json()['choices'][0]['message']['content'] ?? 'Unable to generate response.';
    }

    private function buildSystemPrompt(bool $isRomanUrdu): string
    {
        $basePrompt = "You are an advanced sales forecasting assistant for a retail ERP system. You can:
        - Analyze sales data and trends
        - Generate accurate forecasts
        - Suggest deals and packages
        - Predict daily/weekly sales
        - Provide actionable insights
        
        Always provide specific numbers, percentages, and actionable recommendations.
        Format responses with clear sections and bullet points.";
        
        if ($isRomanUrdu) {
            $basePrompt .= "\n\nIMPORTANT: Respond in Roman Urdu (Urdu written in English letters). Use simple, clear Roman Urdu that's easy to understand.";
        }
        
        return $basePrompt;
    }

    private function buildContextPrompt(array $data): string
    {
        return "Current analysis data: " . json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    // Additional helper methods
    private function getSlowMovingItems(array $context): array
    {
        return DB::table('sales_receipt_details as srd')
            ->join('inventory_general as ig', 'srd.item_code', '=', 'ig.id')
            ->select('srd.item_code', 'ig.product_name', DB::raw('SUM(srd.total_qty) as total_sold'))
            ->where('srd.created_at', '>=', now()->subDays(30))
            ->groupBy('srd.item_code', 'ig.product_name')
            ->orderBy('total_sold', 'asc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    private function getPopularCombinations(array $context): array
    {
        // Simplified - in real implementation, use market basket analysis
        return DB::table('sales_receipt_details as srd')
            ->join('inventory_general as ig', 'srd.item_code', '=', 'ig.id')
            ->select('srd.item_code', 'ig.product_name', DB::raw('SUM(srd.total_qty) as total_sold'))
            ->where('srd.created_at', '>=', now()->subDays(30))
            ->groupBy('srd.item_code', 'ig.product_name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return ['items' => [$item]];
            })
            ->toArray();
    }

    private function calculateOptimalDiscount($slowItem, $combo): float
    {
        // Simple discount calculation - can be enhanced with ML
        return rand(10, 25); // 10-25% discount
    }

    private function predictDealImpact($slowItem, $combo): array
    {
        return [
            'expected_sales_increase' => rand(15, 40) . '%',
            'inventory_turnover_improvement' => rand(20, 50) . '%'
        ];
    }

    private function explainDealLogic(array $deals): string
    {
        return "Deals are suggested based on combining slow-moving items with popular products to increase overall sales velocity and reduce inventory holding costs.";
    }

    private function getHistoricalPattern(array $dateInfo, array $context): array
    {
        // Get same day/period data from previous weeks
        return [];
    }

    private function getSeasonalFactors(array $dateInfo): array
    {
        $date = Carbon::parse($dateInfo['start']);
        return [
            'month_factor' => $this->getMonthlyFactor($date->month),
            'day_of_week_factor' => $this->getDayOfWeekFactor($date->dayOfWeek),
            'holiday_factor' => $this->getHolidayFactor($date)
        ];
    }

    private function getTrendFactors(array $context): array
    {
        return [
            'overall_trend' => 'positive', // This would be calculated from actual data
            'category_trends' => [],
            'market_conditions' => 'stable'
        ];
    }

    private function calculatePrediction($historical, $seasonal, $trend): array
    {
        // Simplified prediction - in real implementation, use ML models
        $baseSales = 10000; // This would come from historical data
        $seasonalMultiplier = ($seasonal['month_factor'] + $seasonal['day_of_week_factor']) / 2;
        
        return [
            'amount' => round($baseSales * $seasonalMultiplier, 2),
            'quantity' => rand(50, 200),
            'transactions' => rand(20, 80)
        ];
    }

    private function calculateConfidence($historical): float
    {
        return rand(75, 95) / 100; // 75-95% confidence
    }

    private function getMonthlyFactor(int $month): float
    {
        // Seasonal factors by month (1.0 = average)
        $factors = [1 => 0.9, 2 => 0.85, 3 => 1.1, 4 => 1.05, 5 => 1.15, 6 => 1.2, 
                   7 => 1.25, 8 => 1.2, 9 => 1.1, 10 => 1.05, 11 => 1.3, 12 => 1.4];
        return $factors[$month] ?? 1.0;
    }

    private function getDayOfWeekFactor(int $dayOfWeek): float
    {
        // 0 = Sunday, 6 = Saturday
        $factors = [0 => 1.2, 1 => 0.8, 2 => 0.9, 3 => 0.95, 4 => 1.1, 5 => 1.3, 6 => 1.4];
        return $factors[$dayOfWeek] ?? 1.0;
    }

    private function getHolidayFactor(Carbon $date): float
    {
        // Check if date is near holidays - simplified
        return 1.0;
    }

    private function analyzeSalesData(array $salesData): array
    {
        if (empty($salesData)) {
            return [
                'total_sales' => 0,
                'total_quantity' => 0,
                'avg_transaction_value' => 0,
                'top_products' => [],
                'product_count' => 0
            ];
        }
        
        $totalAmount = 0;
        $totalQuantity = 0;
        $totalTransactions = 0;
        
        foreach ($salesData as $item) {
            $totalAmount += is_object($item) ? $item->total_amount : $item['total_amount'];
            $totalQuantity += is_object($item) ? $item->total_quantity : $item['total_quantity'];
            $totalTransactions += is_object($item) ? $item->transaction_count : $item['transaction_count'];
        }
        
        $avgTransactionValue = $totalTransactions > 0 ? $totalAmount / $totalTransactions : 0;
        
        return [
            'total_sales' => $totalAmount,
            'total_quantity' => $totalQuantity,
            'avg_transaction_value' => round($avgTransactionValue, 2),
            'top_products' => array_slice($salesData, 0, 5),
            'product_count' => count($salesData)
        ];
    }

    private function generateInsights(array $analysis): array
    {
        $insights = [];
        
        if ($analysis['avg_transaction_value'] > 1000) {
            $insights[] = "High average transaction value indicates premium customer base";
        }
        
        if ($analysis['product_count'] < 10) {
            $insights[] = "Limited product variety - consider expanding product range";
        }
        
        return $insights;
    }

    private function generateRecommendations(array $forecast, array $trends): array
    {
        $recommendations = [];
        
        if ($forecast['growth_rate'] > 10) {
            $recommendations[] = "Strong growth expected - increase inventory levels";
        } elseif ($forecast['growth_rate'] < -5) {
            $recommendations[] = "Declining trend - implement promotional strategies";
        }
        
        return $recommendations;
    }

    private function generateGeneralResponse(string $userText, array $context): array
    {
        return [
            'type' => 'general',
            'message' => 'I can help you with sales forecasting, reports, deal creation, and sales predictions. Please specify what you need.',
            'suggestions' => [
                'Show me yesterday\'s sales',
                'Predict tomorrow\'s sales',
                'Create a deal for slow-moving items',
                'Forecast next week\'s sales'
            ]
        ];
    }
}
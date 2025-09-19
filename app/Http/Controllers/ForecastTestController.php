<?php

namespace App\Http\Controllers;

use App\Services\SalesForecastService;
use Illuminate\Http\Request;

class ForecastTestController extends Controller
{
    public function test(SalesForecastService $forecastService)
    {
        // Test the service with sample data
        $testQueries = [
            "Yesterday ka sales kitna tha?",
            "Predict tomorrow's sales",
            "Create deals for slow moving items",
            "Show me last week trends",
            "Aaj kitni sales hogi?",
            "Pichlay hafta ka forecast dikhao"
        ];
        
        $results = [];
        
        foreach ($testQueries as $query) {
            try {
                $context = [
                    'branch_id' => 'all',
                    'company_id' => 1,
                    'branches' => [1, 2, 3],
                    'user_language' => 'english'
                ];
                
                $result = $forecastService->processQuery($query, $context);
                $results[] = [
                    'query' => $query,
                    'result' => $result,
                    'status' => 'success'
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'query' => $query,
                    'error' => $e->getMessage(),
                    'status' => 'error'
                ];
            }
        }
        
        return response()->json([
            'message' => 'Sales Forecast Service Test Results',
            'results' => $results
        ], 200);
    }
    
    public function testDateParsing(SalesForecastService $forecastService)
    {
        $testDates = [
            "aaj",
            "kal", 
            "yesterday",
            "tomorrow",
            "pichlay 7 din",
            "last week",
            "is hafta",
            "this month",
            "pichlay maheena"
        ];
        
        $results = [];
        
        foreach ($testDates as $dateText) {
            try {
                $dateInfo = $forecastService->parseDateFromText($dateText);
                $results[] = [
                    'input' => $dateText,
                    'parsed' => $dateInfo,
                    'status' => 'success'
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'input' => $dateText,
                    'error' => $e->getMessage(),
                    'status' => 'error'
                ];
            }
        }
        
        return response()->json([
            'message' => 'Date Parsing Test Results',
            'results' => $results
        ], 200);
    }
}
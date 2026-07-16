<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\FbrInvoiceService;
use App\Services\OrderRecalculateService;
use App\Services\SalesReturnDuplicateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SalesReturnController extends Controller
{
    public function duplicateForm()
    {
        return view('v2.sales-return.duplicate');
    }

    public function duplicate(Request $request, SalesReturnDuplicateService $service)
    {
        $request->validate([
            'order_ids' => 'required|string',
        ]);

        $ids = $this->parseIds($request->order_ids);
        if (empty($ids)) {
            return response()->json([
                'status' => 422,
                'message' => 'Please provide at least one valid order ID.',
            ], 422);
        }

        try {
            $result = $service->duplicateMany($ids);

            return response()->json([
                'status' => 200,
                'message' => 'Duplicate process completed.',
                'data' => $result,
            ]);
        } catch (\Throwable $e) {
            Log::error('Sales return duplicate failed: ' . $e->getMessage());

            return response()->json([
                'status' => 500,
                'message' => 'Failed to duplicate orders: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function edit(int $id)
    {
        $order = Order::with(['orderdetails.inventory', 'orderAccountSub', 'orderAccount', 'branchrelation'])
            ->where('id', $id)
            ->where('status', 14)
            ->firstOrFail();

        return view('v2.sales-return.edit', compact('order'));
    }

    public function deleteItem(int $detailId, OrderRecalculateService $service)
    {
        try {
            $result = $service->deleteLineAndRecalculate($detailId);

            return response()->json([
                'status' => 200,
                'message' => 'Item deleted and totals recalculated.',
                'data' => $result,
            ]);
        } catch (\Throwable $e) {
            Log::error('Sales return item delete failed: ' . $e->getMessage());

            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function fbrIndex(Request $request)
    {
        $query = Order::with(['branchrelation', 'orderAccountSub'])
            ->where('status', 14)
            ->orderByDesc('id');

        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }
        if ($request->filled('branch') && $request->branch !== 'all') {
            $query->where('branch', $request->branch);
        }
        if ($request->filled('fbr_status')) {
            if ($request->fbr_status === 'pending') {
                $query->where(function ($q) {
                    $q->whereNull('fbrInvNumber')->orWhere('fbrInvNumber', '');
                });
            } elseif ($request->fbr_status === 'sent') {
                $query->whereNotNull('fbrInvNumber')->where('fbrInvNumber', '!=', '');
            }
        }

        $returns = $query->paginate(50)->appends($request->query());

        return view('v2.sales-return.fbr', compact('returns'));
    }

    public function fbrSend(Request $request, FbrInvoiceService $service)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ]);

        $results = $service->sendMany($request->ids, true);

        $successCount = collect($results)->where('success', true)->count();
        $failCount = count($results) - $successCount;

        return response()->json([
            'status' => 200,
            'message' => "FBR send finished. Success: {$successCount}, Failed: {$failCount}",
            'data' => $results,
        ]);
    }

    /**
     * @return array<int>
     */
    private function parseIds(string $input): array
    {
        $parts = preg_split('/[\s,]+/', trim($input), -1, PREG_SPLIT_NO_EMPTY);
        $ids = array_map('intval', $parts ?: []);

        return array_values(array_unique(array_filter($ids, fn ($id) => $id > 0)));
    }
}

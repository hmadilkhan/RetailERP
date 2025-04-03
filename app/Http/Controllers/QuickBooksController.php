<?php

namespace App\Http\Controllers;

use App\Services\QuickBooks\QuickBooksCustomerService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QuickBooksController extends Controller
{
    protected $customerService;

    public function __construct(QuickBooksCustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function addCustomer(Request $request)
    {
        $result = $this->customerService->createCustomer($request->all());

        if (isset($result['error'])) {
            return response()->json(['success' => false, 'message' => $result['message']], 400);
        }

        return response()->json(['success' => true, 'customer' => $result], 201);
    }

    public function updateCustomer(Request $request, $id)
    {
        $result = $this->customerService->updateCustomer($id, $request->all());

        if (isset($result['error'])) {
            return response()->json(['success' => false, 'message' => $result['message']], 400);
        }

        return response()->json(['success' => true, 'customer' => $result], 200);
    }

    public function deleteCustomer($id)
    {
        try {
            $deleted = $this->customerService->deleteEntity('Customer', $id);
            if (!$deleted) {
                return response()->json(['success' => false, 'message' => 'Customer deletion failed.'], 400);
            }
            return response()->json(['success' => true, 'message' => 'Customer deleted successfully.'], 200);
        } catch (Exception $e) {
            Log::error('QuickBooks Customer Deletion Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

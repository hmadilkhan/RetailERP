<?php

namespace App\Http\Controllers;

use App\Services\QuickBooks\QuickBooksCustomerService;
use App\Services\QuickBooks\QuickBooksAuthService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QuickBooksController extends Controller
{
    protected $authService;

    public function __construct(QuickBooksAuthService $authService)
    {
        $this->authService = $authService;
    }

    public function addCustomer(Request $request)
    {
        $result = $this->customerService($request)->createCustomer($request->all());

        if (isset($result['error'])) {
            return response()->json(['success' => false, 'message' => $result['message']], 400);
        }

        return response()->json(['success' => true, 'customer' => $result], 201);
    }

    public function updateCustomer(Request $request, $id)
    {
        $result = $this->customerService($request)->updateCustomer($id, $request->all());

        if (isset($result['error'])) {
            return response()->json(['success' => false, 'message' => $result['message']], 400);
        }

        return response()->json(['success' => true, 'customer' => $result], 200);
    }

    public function deleteCustomer(Request $request, $id)
    {
        try {
            $deleted = $this->customerService($request)->deleteEntity('Customer', $id);
            if (!$deleted) {
                return response()->json(['success' => false, 'message' => 'Customer deletion failed.'], 400);
            }
            return response()->json(['success' => true, 'message' => 'Customer deleted successfully.'], 200);
        } catch (Exception $e) {
            Log::error('QuickBooks Customer Deletion Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function customerService(Request $request): QuickBooksCustomerService
    {
        $companyId = $request->session()->get('company_id') ?? optional(auth()->user())->company_id;

        if (!$companyId) {
            abort(422, 'Company context is required for QuickBooks customer operations.');
        }

        return new QuickBooksCustomerService($this->authService, $companyId);
    }
}

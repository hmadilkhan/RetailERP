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

    /** Phase 3.6: "Connect to QuickBooks" — Intuit login pe bhejo (sirf Admin / Sub-Admin, apni company ke liye). */
    public function connect(Request $request)
    {
        abort_unless(in_array((int) session('roleId'), [1, 2], true), 403);
        $state = bin2hex(random_bytes(16));
        $request->session()->put('qbo_oauth', ['state' => $state, 'company_id' => (int) session('company_id')]);
        return redirect()->away($this->authService->authorizationUrl($state));
    }

    /** Intuit wapas yahan bhejta hai (QUICKBOOKS_REDIRECT_URI isi URL pe hona chahiye). */
    public function callback(Request $request)
    {
        $pending = $request->session()->pull('qbo_oauth');
        if (!$pending || !hash_equals($pending['state'], (string) $request->query('state')) || (int) $pending['company_id'] !== (int) session('company_id')) {
            return redirect('quickbooks-sync')->with('qbo_error', 'QuickBooks connection could not be verified. Please try again.');
        }
        if ($request->query('error') || !$request->query('code') || !$request->query('realmId')) {
            return redirect('quickbooks-sync')->with('qbo_error', 'QuickBooks connection was cancelled.');
        }
        try {
            $this->authService->connect((int) $pending['company_id'], (string) $request->query('code'), (string) $request->query('realmId'));
        } catch (Exception $e) {
            Log::error('QuickBooks connect failed: ' . $e->getMessage());
            return redirect('quickbooks-sync')->with('qbo_error', 'QuickBooks connection failed: ' . $e->getMessage());
        }
        return redirect('quickbooks-sync')->with('qbo_message', 'QuickBooks connected.');
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

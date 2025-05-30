<?php

namespace App\Services\QuickBooks;

use Exception;
use Illuminate\Support\Facades\Log;
use QuickBooksOnline\API\Facades\Customer;

class QuickBooksCustomerService extends QuickBooksService
{
    public function __construct(QuickBooksAuthService $authService, $companyId)
    {
        parent::__construct($authService, $companyId); // ✅ Make sure this is called
    }
    
    public function createCustomer(array $customerData)
    {
        Log::info('QuickBooks Customer Data: ' . $customerData);
        try {
            $customer = Customer::create($customerData);
            $response = $this->dataService->Add($customer);

            // Check for API errors
            if (!$response) {
                $error = $this->dataService->getLastError();
                if ($error) {
                    throw new Exception("QuickBooks Error: " . $error->getResponseBody());
                }
            }

            return $response;
        } catch (Exception $e) {
            Log::error('QuickBooks Customer Creation Error: ' . $e->getMessage());
            return ['error' => 'Customer creation failed.', 'message' => $e->getMessage()];
        }
    }

    public function updateCustomer($id, array $customerData)
    {
        try {
            $customer = $this->findById('Customer', $id);
            if (!$customer) {
                throw new Exception("Customer not found in QuickBooks.");
            }

            $updatedCustomer = Customer::update($customer, $customerData);
            $response = $this->dataService->Update($updatedCustomer);

            if (!$response) {
                $error = $this->dataService->getLastError();
                if ($error) {
                    throw new Exception("QuickBooks Error: " . $error->getResponseBody());
                }
            }

            return $response;
        } catch (Exception $e) {
            Log::error('QuickBooks Customer Update Error: ' . $e->getMessage());
            return ['error' => 'Customer update failed.', 'message' => $e->getMessage()];
        }
    }

    public function deleteCustomer($id)
    {
        try {
            $customer = $this->findById('Customer', $id);
            if (!$customer) {
                throw new Exception("Customer not found in QuickBooks.");
            }

            $response = $this->dataService->Delete($customer);

            if (!$response) {
                $error = $this->dataService->getLastError();
                if ($error) {
                    throw new Exception("QuickBooks Error: " . $error->getResponseBody());
                }
            }

            return $response;
        } catch (Exception $e) {
            Log::error('QuickBooks Customer Delete Error: ' . $e->getMessage());
            return ['error' => 'Customer Delete failed.', 'message' => $e->getMessage()];
        }
    }
}

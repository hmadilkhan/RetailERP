<?php

namespace App\Services\QuickBooks;

use Exception;
use Illuminate\Support\Facades\Log;
use QuickBooksOnline\API\Facades\Invoice;

class QuickBooksInvoiceService extends QuickBooksService
{
    public function __construct(QuickBooksAuthService $authService, $companyId)
    {
        parent::__construct($authService, $companyId); // ✅ Make sure this is called
    }

    public function createInvoice(array $invoiceData)
    {
        try {
            $item = Invoice::create($invoiceData);
            $response = $this->dataService->Add($item);

            // Check for API errors
            if (!$response) {
                $error = $this->dataService->getLastError();
                if ($error) {
                    throw new Exception("QuickBooks Error: " . $error->getResponseBody());
                }
            }

            return $response;
        } catch (Exception $e) {
            Log::error('QuickBooks Item Creation Error: ' . $e->getMessage());
            return ['error' => 'Item creation failed.', 'message' => $e->getMessage()];
        }
    }

    public function updateInvoice($id, array $invoiceData)
    {
        try {
            $item = $this->findById('invoice', $id);
            if (!$item) {
                throw new Exception("Invoice not found in QuickBooks.");
            }

            $updatedInvoice = Invoice::update($item, $invoiceData);
            $response = $this->dataService->Update($updatedInvoice);

            if (!$response) {
                $error = $this->dataService->getLastError();
                if ($error) {
                    throw new Exception("QuickBooks Error: " . $error->getResponseBody());
                }
            }

            return $response;
        } catch (Exception $e) {
            Log::error('QuickBooks Item Update Error: ' . $e->getMessage());
            return ['error' => 'Item update failed.', 'message' => $e->getMessage()];
        }
    }

    public function deleteInvoice($id)
    {
        try {
            $invoice = $this->findById('invoice', $id);
            if (!$invoice) {
                throw new Exception("Invoice not found in QuickBooks.");
            }

            $response = $this->dataService->Delete($invoice);

            if (!$response) {
                $error = $this->dataService->getLastError();
                if ($error) {
                    throw new Exception("QuickBooks Error: " . $error->getResponseBody());
                }
            }

            return $response;
        } catch (Exception $e) {
            Log::error('QuickBooks Invoice Delete Error: ' . $e->getMessage());
            return ['error' => 'Invoice delete failed.', 'message' => $e->getMessage()];
        }
    }
}

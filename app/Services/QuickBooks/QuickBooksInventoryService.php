<?php

namespace App\Services\QuickBooks;

use Exception;
use Illuminate\Support\Facades\Log;
use QuickBooksOnline\API\Facades\Item;

class QuickBooksInventoryService extends QuickBooksService
{
    public function __construct(QuickBooksAuthService $authService, $companyId)
    {
        parent::__construct($authService, $companyId); // ✅ Make sure this is called
    }
    
    public function createItem(array $productData)
    {
        try {
            $item = Item::create($productData);
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

    public function updateItem($id, array $productData)
    {
        try {
            $item = $this->findById('Item', $id);
            if (!$item) {
                throw new Exception("Item not found in QuickBooks.");
            }

            $updatedItem = Item::update($item, $productData);
            $response = $this->dataService->Update($updatedItem);

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
}

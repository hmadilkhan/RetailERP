<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Report;
use Exception;

class GenerateDailyUsageCommand extends Command
{
    protected $signature = 'usage:generate-daily';
    protected $description = 'Generate daily recipe usage and deduct inventory stock';

    public function handle()
    {
        $from = date('Y-m-d', strtotime('-1 day'));
        
        try {
            $permissions = DB::table('daily_recipe_deduction_permission')
                ->where('status', 1)
                ->get();

            if ($permissions->isEmpty()) {
                $this->info('No active permissions found.');
                return;
            }

            $report = new Report();
            $to = $from;

            foreach ($permissions as $permission) {
                try {
                    $this->info("Processing Company ID: {$permission->company_id}, Branch ID: {$permission->branch_id}");

                    $recipyItems = $report->getRecipeDetails($permission->company_id);
                    $totalSaleItems = $report->getSalesItemByDate($from, $to, $permission->company_id, $permission->branch_id);

                    foreach ($totalSaleItems as $item) {
                        try {
                            $filteredArray = Arr::where($recipyItems, function ($value) use ($item) {
                                return $value->recipy_id == $item->recipy_id;
                            });

                            foreach ($filteredArray as $recipyItem) {
                                try {
                                    $previousStock = DB::table('inventory_stock')->where('product_id', $recipyItem->item_id)->get();
                                    
                                    $stockResult = $this->inventStockDetection($permission->branch_id, $recipyItem->item_id, ($item->totalqty * $recipyItem->usage_qty), "");
                                    
                                    if ($stockResult === 0) {
                                        $this->logError($permission->company_id, $permission->branch_id, $item->recipy_id, $recipyItem->item_id, 'STOCK_NOT_FOUND', 'No inventory stock found for item', null, $from);
                                    }
                                    
                                    $currentStock = DB::table('inventory_stock')->where('product_id', $recipyItem->item_id)->get();

                                    DB::table('daily_recipe_usage')->insert([
                                        'item_id' => $recipyItem->item_id,
                                        'usage_qty' => $recipyItem->usage_qty,
                                        'total_qty' => $item->totalqty,
                                        'total_usage' => $item->totalqty * $recipyItem->usage_qty,
                                        'recipy_id' => $item->recipy_id,
                                        'opening_id' => $item->opening_id,
                                        'original_date' => $item->date,
                                        'previous_stock' => ($previousStock->isEmpty() == 0 ? $previousStock[0]->balance : 0),
                                        'current_stock' => ($currentStock->isEmpty() == 0 ? $currentStock[0]->balance : 0),
                                    ]);
                                } catch (Exception $e) {
                                    $this->logError($permission->company_id, $permission->branch_id, $item->recipy_id, $recipyItem->item_id, 'ITEM_PROCESSING_ERROR', $e->getMessage(), $e->getTraceAsString(), $from);
                                    $this->error("Error processing item {$recipyItem->item_id}: {$e->getMessage()}");
                                }
                            }
                        } catch (Exception $e) {
                            $this->logError($permission->company_id, $permission->branch_id, $item->recipy_id, null, 'RECIPE_PROCESSING_ERROR', $e->getMessage(), $e->getTraceAsString(), $from);
                            $this->error("Error processing recipe {$item->recipy_id}: {$e->getMessage()}");
                        }
                    }
                } catch (Exception $e) {
                    $this->logError($permission->company_id, $permission->branch_id, null, null, 'PERMISSION_PROCESSING_ERROR', $e->getMessage(), $e->getTraceAsString(), $from);
                    $this->error("Error processing company {$permission->company_id}, branch {$permission->branch_id}: {$e->getMessage()}");
                }
            }

            $this->info('Daily usage generated successfully.');
        } catch (Exception $e) {
            $this->logError(null, null, null, null, 'COMMAND_EXECUTION_ERROR', $e->getMessage(), $e->getTraceAsString(), $from);
            $this->error("Command execution failed: {$e->getMessage()}");
        }
    }

    private function logError($companyId, $branchId, $recipeId, $itemId, $errorType, $errorMessage, $errorDetails, $processDate)
    {
        try {
            DB::table('daily_usage_error_logs')->insert([
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'recipe_id' => $recipeId,
                'item_id' => $itemId,
                'error_type' => $errorType,
                'error_message' => $errorMessage,
                'error_details' => $errorDetails,
                'process_date' => $processDate,
                'created_at' => now(),
            ]);
        } catch (Exception $e) {
            $this->error("Failed to log error: {$e->getMessage()}");
        }
    }

    private function inventStockDetection($branchId, $itemCode, $totalQty, $status = "")
    {
        try {
            if (empty($branchId) || $branchId <= 0 || empty($itemCode) || $itemCode <= 0) {
                return 0;
            }

            $result = DB::select("SELECT * FROM inventory_stock WHERE product_id = ? and branch_id = ? and status_id IN(1,3)", [$itemCode, $branchId]);

            if (empty($result)) {
                return 0;
            }

            $updatedstock = 0;

            if ($status == "Open") {
                $weightQty = DB::select("SELECT weight_qty FROM inventory_general WHERE id = ?", [$itemCode]);
                if (empty($weightQty) || $weightQty[0]->weight_qty == 0) {
                    throw new Exception("Invalid weight quantity for item {$itemCode}");
                }
                $qty = $totalQty / $weightQty[0]->weight_qty;
                $updatedstock = $qty;
            } else {
                $updatedstock = $totalQty;
            }

            for ($s = 0; $s < count($result); $s++) {
                $value = DB::select("SELECT * FROM inventory_stock WHERE product_id = ? and branch_id = ? and status_id IN(1,3) LIMIT 1", [$itemCode, $branchId]);
                
                if (empty($value)) {
                    break;
                }

                $updatedstock = ($updatedstock - $value[0]->balance);

                if ($updatedstock > 0) {
                    DB::table("inventory_stock")->where("stock_id", $value[0]->stock_id)->update([
                        "balance" => 0,
                        "status_id" => 2,
                    ]);
                } else if ($updatedstock < 0) {
                    $updatedstock = abs($updatedstock);
                    DB::table("inventory_stock")->where("stock_id", $value[0]->stock_id)->update([
                        "balance" => $updatedstock,
                        "status_id" => 1,
                    ]);
                    break;
                } else {
                    DB::table("inventory_stock")->where("stock_id", $value[0]->stock_id)->update([
                        "balance" => 0,
                        "status_id" => 2,
                    ]);
                    break;
                }
            }
            return 1;
        } catch (Exception $e) {
            throw new Exception("Stock detection failed: " . $e->getMessage());
        }
    }
}

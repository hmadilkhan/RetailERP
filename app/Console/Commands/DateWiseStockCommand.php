<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\DailyStock;
use App\Models\InventoryStock;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DateWiseStockCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:date-wise-stock-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save Opening and Closing Stock Date Wise';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $branch = 283;
        $date = date("Y-m-d");
        $yesterday = date('Y-m-d',strtotime("-1 days"));
        // This change will add to 04-10-2024 and starts run on 05-10-2024
        $branches = Branch::where("company_id",102)->get();
        
        foreach ($branches as $key => $branch) {
            $stocks = InventoryStock::where("branch_id", $branch->branch_id)->where("status_id", 1)->groupBy("product_id")->select("product_id", DB::raw('SUM(inventory_stock.balance) As stock'))->get();
            foreach ($stocks as $key => $stock) {
                DailyStock::insert([
                    "company_id" => 102,
                    "branch_id" => $branch->branch_id,
                    "product_id" =>  $stock->product_id,
                    "opening_stock" => $stock->stock,
                ]);
                DailyStock::where(DB::raw('Date(created_at)'),$yesterday)->where("product_id",$stock->product_id)->update(["closing_stock" => $stock->stock]);
            }
        }

        $this->info("Stock Added");
    }
}

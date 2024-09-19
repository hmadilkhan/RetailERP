<?php

namespace App\Console\Commands;

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
        $date = "2024-09-18";

        $stocks = InventoryStock::where("branch_id", $branch)->where("status_id", 1)->groupBy("product_id")->select("product_id", DB::raw('SUM(inventory_stock.balance) As stock'))->get();
        foreach ($stocks as $key => $stock) {
            DailyStock::insert([
                "company_id" => 102,
                "branch_id" => $branch,
                "product_id" =>  $stock->product_id,
                "opening_stock" => $stock->stock,
            ]);
        }
        $this->info("Stock Added");
    }
}

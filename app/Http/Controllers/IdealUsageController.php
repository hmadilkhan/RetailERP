<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\report;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;
use PDF,Auth;
use \Illuminate\Support\Arr;



class IdealUsageController extends Controller
{
	public function generateDailyUsageFromWebservice(Request $request)
	{
		
		$report = new Report();
		$from = ($request->from != "" ? $request->from : date("Y-m-d"));
		$to = ($request->to != "" ? $request->to : date("Y-m-d"));
		$company = $request->company;
		$branch = $request->branch;

		$recipyItems = $report->getRecipeDetails($company);
		$totalSaleItems = $report->getSalesItemByDate($from,$to,$company,$branch);
		$allItemUsage = $report->getAllItemUsage($from,$to);
		$totalItemsArray = [];
		$totals= [];
		return $recipyItems;
		
		foreach($totalSaleItems as $item){
			
			$filteredArray = Arr::where($recipyItems, function ($value, $key) use ($item) {
				return $value->recipy_id == $item->recipy_id;
			});
			
			foreach($filteredArray as $recipyItem){
				$recipyitem = [
					"item_id" => $recipyItem->item_id,
					"item_name" => $recipyItem->product_name,
					"uom" => $recipyItem->uom,
					"usage_qty" => $recipyItem->usage_qty,
					"total_qty" => $item->totalqty,
					"total_usage" => $item->totalqty * $recipyItem->usage_qty,
					"recipy_id" => $item->recipy_id,
				];
				array_push($totalItemsArray,$recipyitem);
				$previousStock = DB::table("inventory_stock")->where("product_id",$recipyItem->item_id)->get();
				// $this->invent_stock_detection(session("branch"), $recipyItem->item_id, ( $item->totalqty * $recipyItem->usage_qty), "");
				$currentStock = DB::table("inventory_stock")->where("product_id",$recipyItem->item_id)->get();
				DB::table("daily_recipe_usage")->insert([
					"item_id" => $recipyItem->item_id,
					"usage_qty" => $recipyItem->usage_qty,
					"total_qty" => $item->totalqty,
					"total_usage" => $item->totalqty * $recipyItem->usage_qty,
					"recipy_id" => $item->recipy_id,
					"opening_id" => $item->opening_id,
					"original_date" => $item->date,
					"previous_stock" => ($previousStock->isEmpty() == 0 ? $previousStock[0]->balance : 0),
					"current_stock" => ($previousStock->isEmpty() == 0 ? $currentStock[0]->balance : 0),
				]);
			}
		}
	}
}
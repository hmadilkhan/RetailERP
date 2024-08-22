<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\report;
use App\Models\Branch;
use App\Models\SalesOpening;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class IsdbDatewiseExport  implements WithMultipleSheets
{
	protected $report;
	protected $request;
	
	public function __construct(report $report,Request $request)
	{
		$this->report = $report;
		$this->request = $request;
	}
	
	public function getItemSalesQuery($date1,$date2,$request)
	{
		$openingIds = SalesOpening::whereBetween("date", [$date1, $date2])->where("terminal_id",$request->terminal)->pluck("opening_id");	
		return OrderDetails::with("order","inventory:id,item_code,product_name,weight_qty","order.terminal:terminal_id,terminal_name","order.branchrelation:branch_id,branch_name,code")
			->whereHas('order', function($q) use ($openingIds,$request,$date1, $date2) {
				$q->when($request->declaration == "declaration", function ($q) use ($request,$openingIds) {
					$q->whereIn("opening_id",$openingIds);
				},
				function ($q) use ($request,$date1,$date2) {
					return $q->whereBetween("date", [$date1, $date2]);;
				});
				$q->where("branch",$request->branch)
				->where("terminal_id",$request->terminal);
			})
			
			->select("receipt_detail_id","receipt_id","item_code","item_price",DB::raw('SUM(total_qty) as total_qty'),DB::raw('AVG(item_price) as avg_price'),DB::raw('SUM(item_price*total_qty) as total_amount'))
			->groupBy("item_code","item_price")
			->orderBy("item_code","asc")
			->get();
	}
	
    public function sheets(): array
    {
        $sheets = [];
        $maxPage = 10;
		$branch = Branch::with("company:company_id,name")->where("branch_id",$request->branch)->first();
		$intervaldates = $this->generateDateRange(Carbon::parse($this->request->fromdate),Carbon::parse($this->request->todate));
        foreach($intervaldates as $date) {
			$isdb = [];
			$record = $this->getItemSalesQuery($date,$date,$this->request);
			$datearray = [
				"from" => $date,
				"to" => $date,
			];
            $sheets["Date :".$date] = new ItemSaleReportExport($record,$branch,$datearray);
        }
        return $sheets;
    }
	
	public function generateDateRange(Carbon $start_date, Carbon $end_date)
	{
		$dates = [];

		for($date = $start_date->copy(); $date->lte($end_date); $date->addDay()) {
			$dates[] = $date->format('Y-m-d');
		}

		return $dates;
	}
}

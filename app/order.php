<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\SalesOpening;
use Carbon\Carbon;

class order extends Model
{
	public function getOrders($first, $second, $status, $customer, $receipt, $mode, $delFrom, $delTo, $branch, $terminal, $payMode)
	{
		$filter = "";
		if ($branch == "") {
			$branch = (session("roleId") != 2 ? session("branch") : "");
		}
		/********************************** ORDERS MODE ********************************/
		if ($mode != "") {
			if ($filter  == "") {
				$filter .= " where a.order_mode_id = '" . $mode . "'";
			} else {
				$filter .= " and a.order_mode_id = '" . $mode . "'";
			}
		}
		/********************************** DELIVERY DATES FILTER **********************/
		if ($delFrom != "") {
			if ($filter  == "") {
				$filter .= " where a.delivery_date BETWEEN '" . $delFrom . "' and '" . $delTo . "'";
			} else {
				$filter .= " and a.delivery_date BETWEEN '" . $delFrom . "' and '" . $delTo . "'";
			}
		}
		/********************************** DATES **************************************/
		if ($first != "") {
			if ($filter  == "") {
				$filter .= " where a.date BETWEEN '" . $first . "' and '" . $second . "'";
			} else {
				$filter .= " and a.date BETWEEN '" . $first . "' and '" . $second . "'";
			}
		}
		// else
		// {
		// 	if ($filter  == "") {
		// 		$filter .= " where date BETWEEN '".date("Y-m-d")."' and '".date("Y-m-d")."'";
		// 	}else{
		// 		$filter .= " and date BETWEEN '".date("Y-m-d")."' and '".date("Y-m-d")."'";
		// 	}
		// }
		/********************************** CUSTOMER **************************************/
		if ($customer != "") {
			if ($filter  == "") {
				$filter .= " where a.customer_id = " . $customer . "";
			} else {
				$filter .= " and a.customer_id = " . $customer . "";
			}
		}

		/********************************** RECEIPT **************************************/
		if ($receipt != "") {
			if ($filter  == "") {
				$filter .= " where a.receipt_no = " . $receipt . "";
			} else {
				$filter .= " and a.receipt_no = " . $receipt . "";
			}
		}

		/********************************** STATUS **************************************/
		if ($payMode != "") {
			if ($filter  == "") {
				$filter .= " where f.payment_id = " . $payMode . "";
			} else {
				$filter .= " and f.payment_id = " . $payMode . "";
			}
		}

		//        if($status != "")
		//        {
		//            if ($filter  == "") {
		//                $filter .= " where a.status = ".$status."";
		//            }else{
		//                $filter .= " and a.status = ".$status."";
		//            }
		//
		//        }

		if ($branch == "") {
			if ($filter  == "") {

				if ($branch == "") {
					$filter  .= (session("roleId") == 2 ? " where a.branch IN (SELECT branch_id FROM `branch` WHERE company_id = " . session("company_id") . ") " : " where a.branch = " . $branch . "");
				}

				//				$filter .= (session("roleId") == 2 ? " where a.branch IN (SELECT branch_id FROM `branch` WHERE company_id = ".session("company_id").") " : " where a.branch = ".$branch."" );
			} else {
				$filter  .= (session("roleId") == 2 ? " and a.branch IN (SELECT branch_id FROM `branch` WHERE company_id = " . session("company_id") . ") " : " and a.branch = " . $branch . "");
				//				$filter .= " and a.branch = ".$branch."";
			}
		} else {
			if ($filter  == "") {
				$filter .= " where a.branch = " . $branch . "";
			} else {
				$filter .= " where a.branch = " . $branch . "";
			}
		}

		if ($terminal != "") {
			if ($filter  == "") {
				$filter .= " where a.terminal_id = " . $terminal . "";
			} else {
				$filter .= " and a.terminal_id = " . $terminal . "";
			}
		}

		if ($filter  == "") {
			$filter .= " where a.status != 1";
		} else {
			$filter .= " and a.status != 1";
		}




		$result = DB::select('SELECT a.id,a.receipt_no,b.order_mode,c.name,a.total_amount,d.order_status_name,g.branch_name as branch,h.terminal_name,a.date,a.delivery_date,a.order_mode_id,a.status,c.mobile,e.receive_amount, f.payment_mode,a.fbrInvNumber from sales_receipts a
							INNER JOIN sales_order_mode b on b.order_mode_id = a.order_mode_id
							INNER JOIN customers c on c.id = a.customer_id
							INNER JOIN sales_order_status d on d.order_status_id = a.status
							INNER JOIN branch g on g.branch_id = a.branch
							INNER JOIN terminal_details h on h.terminal_id = a.terminal_id
							INNER JOIN sales_account_general e on e.receipt_id = a.id
							INNER JOIN sales_payment f on f.payment_id = a.payment_id  ' . $filter . '  order by a.id DESC');
		return $result;
	}

	public function getNewPOSOrdersQuery($request, $mode = "")
	{
		$fromDate = "";
		$toDate = "";
		if ($request->deli_from != "" && $request->deli_to != "") {
			$fromDate = $request->deli_from;
			$toDate = $request->deli_to;
		} else if ($request->first != "" && $request->second != "") {
			$fromDate = $request->first;
			$toDate = $request->second;
		}

		if (!empty($request->branch) && $request->branch[0] == "all" && !empty($request->terminal) && $request->terminal[0] == "all") {
			$openingIds = SalesOpening::whereBetween("date", [$fromDate, $toDate])->whereIn("terminal_id", DB::table("terminal_details")->whereIn("branch_id", DB::table("branch")->where("company_id", session("company_id"))->pluck("branch_id"))->pluck("terminal_id"))->pluck("opening_id");
		} else if (!empty($request->branch) &&  $request->branch[0] != "all" && !empty($request->terminal) && $request->terminal[0] == "all") {
			$openingIds = SalesOpening::whereBetween("date", [$fromDate, $toDate])->whereIn("terminal_id", DB::table("terminal_details")->whereIn("branch_id", DB::table("branch")->whereIn("branch_id", $request->branch)->pluck("branch_id"))->pluck("terminal_id"))->pluck("opening_id");
		} else {
			$openingIds = SalesOpening::whereBetween("date", [$fromDate, $toDate])->whereIn("terminal_id", $request->terminal)->pluck("opening_id");
		}
		$query = DB::table("sales_receipts")
			->join("branch", "branch.branch_id", "=", "sales_receipts.branch")
			->join("terminal_details", "terminal_details.terminal_id", "=", "sales_receipts.terminal_id")
			->leftJoin("customers", "customers.id", "=", "sales_receipts.customer_id")
			->join("sales_order_mode", "sales_order_mode.order_mode_id", "=", "sales_receipts.order_mode_id")
			->join("sales_order_status", "sales_order_status.order_status_id", "=", "sales_receipts.status")
			->join("sales_payment", "sales_payment.payment_id", "=", "sales_receipts.payment_id")
			->leftJoin("service_provider_orders", "service_provider_orders.receipt_id", "=", "sales_receipts.id")
			->leftJoin("service_provider_details", "service_provider_details.id", "=", "service_provider_orders.service_provider_id")
			// ->where("sales_receipts.web", "=", 0)

			->when($request->first != "" && $request->second != "" && $request->type == "declaration", function ($query) use ($request, $openingIds) {
				$query->whereIn("sales_receipts.opening_id", $openingIds);
			})
			->when($request->first != "" && $request->second != "" && $request->type == "datewise", function ($query) use ($request) {
				$query->whereBetween("sales_receipts.date", [$request->first, $request->second]);
			})
			->when($request->deli_from != "" && $request->deli_to != "" && $request->type == "declaration", function ($query) use ($request, $openingIds) {
				$query->whereIn("sales_receipts.opening_id", $openingIds);
			})
			->when($request->deli_from != "" && $request->deli_to != "" && $request->type == "datewise", function ($query) use ($request) {
				// $query->whereBetween("sales_receipts.delivery_date", [date("Ymd",strtotime($request->deli_from)), date("Ymd",strtotime($request->deli_to))]);
				$query->whereBetween(DB::raw('DATE(sales_receipts.order_delivery_date)'), [$request->deli_from, $request->deli_to]);
			})
			->when($request->order_no != "", function ($query) use ($request) {
				$query->where('sales_receipts.id', $request->order_no);
			})
			->when($request->machineOrderNo != "", function ($query) use ($request) {
				$query->where('sales_receipts.machine_terminal_count', $request->machineOrderNo);
			})
			->when(!empty($request->branch) &&  $request->branch[0] != 'all', function ($query) use ($request) {
				$query->whereIn('sales_receipts.branch', $request->branch);
			})
			// ->when($request->branch == "" && $request->branch != "all", function ($query) use ($request) {
			// 	$query->where('sales_receipts.branch', session('branch'));
			// })
			->when(!empty($request->branch) && $request->branch[0] == "all", function ($query) use ($request) {
				$query->whereIn('sales_receipts.branch', DB::table("branch")->where("company_id", session("company_id"))->pluck("branch_id"));
			})
			->when(!empty($request->terminal) && $request->terminal[0] != "all", function ($query) use ($request) {
				$query->whereIn('sales_receipts.terminal_id', $request->terminal);
			})
			->when($request->payMode != "", function ($query) use ($request) {
				$query->where('sales_receipts.payment_id', $request->payMode);
			})
			->when($request->mode != "", function ($query) use ($request) {
				$query->where('sales_receipts.order_mode_id', $request->mode);
			})
			->when(!empty($request->receipt), function ($query) use ($request) {
				$query->where('sales_receipts.receipt_no', $request->receipt);
			})
			->when($request->customer != "", function ($query) use ($request) {
				$query->where('sales_receipts.customer_id', $request->customer);
			})
			->when(!empty($request->status) && $request->status[0] != null, function ($query) use ($request) {
				$query->whereIn('sales_receipts.status', $request->status);
			})
			->when($request->customerNo != "", function ($query) use ($request) {
				$query->where('customers.mobile', $request->customerNo);
			})
			->when($request->sales_tax != "", function ($query) use ($request) {
				$query->where("sales_receipts.fbrInvNumber", '!=', "");
			})
			->when($request->salesperson != "" && $request->salesperson != "all", function ($query) use ($request) {
				$query->where("sales_receipts.sales_person_id", '=', $request->salesperson);
			})
			->when($request->category != "" && $request->category != "all", function ($query) use ($request) {
				$query->where("sales_receipts.web", "=", $request->category);
			})

			->select("sales_receipts.*", "sales_receipts.time", "branch.branch_name", "terminal_details.terminal_name", "customers.name", "sales_order_mode.order_mode", "sales_order_status.order_status_name", "sales_payment.payment_mode", "service_provider_details.provider_name", DB::raw("(Select COUNT(*) from sales_receipt_details where receipt_id = sales_receipts.id) as itemcount"), DB::raw("(Select SUM(total_qty) from sales_receipt_details where receipt_id = sales_receipts.id) as itemstotalqty"))
			->orderBy("sales_receipts.id", "desc");
		// ->toSql();
		// ->paginate(100);
		// Call get() or paginate() based on the mode
		if ($mode == "report") {
			$results = $query->get();
		} else {
			$results = $query->paginate(100);
			// $results = $query->toSql();
		}

		return $results;
	}

	public function getTotalAndSumofOrdersQuery($request)
	{
		$fromDate = "";
		$toDate = "";
		if ($request->deli_from != "" && $request->deli_to != "") {
			$fromDate = $request->deli_from;
			$toDate = $request->deli_to;
		} else if ($request->first != "" && $request->second != "") {
			$fromDate = $request->first;
			$toDate = $request->second;
		}

		if (!empty($request->branch) && $request->branch[0] == "all" && !empty($request->terminal) && $request->terminal[0] == "all") {
			$openingIds = SalesOpening::whereBetween("date", [$fromDate, $toDate])->whereIn("terminal_id", DB::table("terminal_details")->whereIn("branch_id", DB::table("branch")->where("company_id", session("company_id"))->pluck("branch_id"))->pluck("terminal_id"))->pluck("opening_id");
		} else if (!empty($request->branch) &&  $request->branch[0] != "all" && !empty($request->terminal) && $request->terminal[0] == "all") {
			$openingIds = SalesOpening::whereBetween("date", [$fromDate, $toDate])->whereIn("terminal_id", DB::table("terminal_details")->whereIn("branch_id", DB::table("branch")->whereIn("branch_id", $request->branch)->pluck("branch_id"))->pluck("terminal_id"))->pluck("opening_id");
		} else {
			$openingIds = SalesOpening::whereBetween("date", [$fromDate, $toDate])->whereIn("terminal_id", $request->terminal)->pluck("opening_id");
		}

		return DB::table("sales_receipts")
			->leftjoin("sales_order_status", "sales_order_status.order_status_id", "=", "sales_receipts.status")
			->leftJoin("customers", "customers.id", "=", "sales_receipts.customer_id")

			->when($request->first != "" && $request->second != "" && $request->type == "declaration", function ($query) use ($request, $openingIds) {
				$query->whereIn("sales_receipts.opening_id", $openingIds);
			})
			->when($request->first != "" && $request->second != "" && $request->type == "datewise", function ($query) use ($request) {
				$query->whereBetween("sales_receipts.date", [$request->first, $request->second]);
			})
			->when($request->deli_from != "" && $request->deli_to != "" && $request->type == "declaration", function ($query) use ($request, $openingIds) {
				$query->whereIn("sales_receipts.opening_id", $openingIds);
			})
			->when($request->deli_from != "" && $request->deli_to != "" && $request->type == "datewise", function ($query) use ($request) {
				// $query->whereBetween("sales_receipts.delivery_date", [date("Ymd",strtotime($request->deli_from)), date("Ymd",strtotime($request->deli_to))]);
				$query->whereBetween(DB::raw('DATE(sales_receipts.order_delivery_date)'), [$request->deli_from, $request->deli_to]);
			})
			->when($request->order_no != "", function ($query) use ($request) {
				$query->where('sales_receipts.id', $request->order_no);
			})
			->when($request->machineOrderNo != "", function ($query) use ($request) {
				$query->where('sales_receipts.machine_terminal_count', $request->machineOrderNo);
			})
			->when(!empty($request->branch) &&  $request->branch[0] != 'all', function ($query) use ($request) {
				$query->whereIn('sales_receipts.branch', $request->branch);
			})
			// ->when($request->branch == "" && $request->branch != "all", function ($query) use ($request) {
			// 	$query->where('sales_receipts.branch', session('branch'));
			// })
			->when(!empty($request->branch) && $request->branch[0] == "all", function ($query) use ($request) {
				$query->whereIn('sales_receipts.branch', DB::table("branch")->where("company_id", session("company_id"))->pluck("branch_id"));
			})
			->when(!empty($request->terminal) && $request->terminal[0] != "all", function ($query) use ($request) {
				$query->whereIn('sales_receipts.terminal_id', $request->terminal);
			})
			->when($request->payMode != "", function ($query) use ($request) {
				$query->where('sales_receipts.payment_id', $request->payMode);
			})
			->when($request->mode != "", function ($query) use ($request) {
				$query->where('sales_receipts.order_mode_id', $request->mode);
			})
			->when($request->receipt != "", function ($query) use ($request) {
				$query->where('sales_receipts.receipt_no', $request->receipt);
			})
			->when($request->customer != "", function ($query) use ($request) {
				$query->where('sales_receipts.customer_id', $request->customer);
			})
			->when(!empty($request->status) && $request->status[0] != null, function ($query) use ($request) {
				$query->whereIn('sales_receipts.status', $request->status);
			})
			->when($request->customerNo != "", function ($query) use ($request) {
				$query->where('customers.mobile', $request->customerNo);
			})
			->when($request->sales_tax != "", function ($query) use ($request) {
				$query->where("sales_receipts.fbrInvNumber", '!=', "");
			})
			->when($request->salesperson != "" && $request->salesperson != "all", function ($query) use ($request) {
				$query->where("sales_receipts.sales_person_id", '=', $request->salesperson);
			})
			->when($request->category != "" && $request->category != "all", function ($query) use ($request) {
				$query->where("sales_receipts.web", "=", $request->category);
			})
			// ->where("sales_receipts.web", "=", 0)
			->selectRaw("COUNT(sales_receipts.id) as totalorders,sales_order_status.order_status_name,SUM(sales_receipts.total_amount) as sales")
			->groupBy("status")
			->get();
	}

	public function orderTimingGraph($request)
	{
		// Get the orders grouped by hour
		$orders = DB::table('sales_receipts')
			->select(DB::raw('HOUR(time) as hour, COUNT(*) as total_orders,SUM(total_amount) as total_amount'))
			->groupBy(DB::raw('HOUR(time)'))
			->whereBetween("date", [$request->first, $request->second])
		
			->when(!empty($request->branch) &&  $request->branch[0] != 'all', function ($query) use ($request) {
				$query->whereIn('sales_receipts.branch', $request->branch);
			})
			->when(!empty($request->branch) && $request->branch[0] == "all", function ($query) {
				$query->whereIn('sales_receipts.branch', DB::table("branch")->where("company_id", session("company_id"))->pluck("branch_id"));
			})
			->when(!empty($request->terminal) && $request->terminal[0] != "all", function ($query) use ($request) {
				$query->whereIn('sales_receipts.terminal_id', $request->terminal);
			})
			->when($request->payMode != "", function ($query) use ($request) {
				$query->where('sales_receipts.payment_id', $request->payMode);
			})
			->when($request->mode != "", function ($query) use ($request) {
				$query->where('sales_receipts.order_mode_id', $request->mode);
			})
			->when($request->receipt != "", function ($query) use ($request) {
				$query->where('sales_receipts.receipt_no', $request->receipt);
			})
			->when($request->customer != "", function ($query) use ($request) {
				$query->where('sales_receipts.customer_id', $request->customer);
			})
			->when(!empty($request->status) && $request->status[0] != null, function ($query) use ($request) {
				$query->whereIn('sales_receipts.status', $request->status);
			})
			->when($request->customerNo != "", function ($query) use ($request) {
				$query->where('customers.mobile', $request->customerNo);
			})
			->when($request->sales_tax != "", function ($query) use ($request) {
				$query->where("sales_receipts.fbrInvNumber", '!=', "");
			})
			->when($request->salesperson != "" && $request->salesperson != "all", function ($query) use ($request) {
				$query->where("sales_receipts.sales_person_id", '=', $request->salesperson);
			})
			->when($request->category != "" && $request->category != "all", function ($query) use ($request) {
				$query->where("sales_receipts.web", "=", $request->category);
			})
			->orderBy('hour')
			->get();
	
		for ($i = 0; $i < 24; $i++) {
			$startTime = Carbon::createFromTime($i)->format('g a');
			$endTime = Carbon::createFromTime($i, 59)->format('g:i A');

			$hourRanges[] = [
				'hour' => $i,
				'hour_range' => $startTime,
				'total_orders' => 0, // Default to 0 orders
				'total_amount' => 0, // Default to 0 orders
			];
		}

		// Merge the query results into the hour range array
		$peakOrders = collect($hourRanges)->map(function ($range) use ($orders) {
			$matchingOrder = $orders->firstWhere('hour', $range['hour']);
			if ($matchingOrder) {
				$range['total_orders'] = $matchingOrder->total_orders;
				$range['total_amount'] = $matchingOrder->total_amount;
			}
			return $range;
		});

		return $peakOrders;
	}

	public function getTotalNofOrdersByStatusQuery($request)
	{
		if ($request->branch == "all") {
			$openingIds = SalesOpening::whereBetween("date", [$request->first, $request->second])->whereIn("terminal_id", DB::table("terminal_details")->whereIn("branch_id", DB::table("branch")->where("company_id", session("company_id"))->pluck("branch_id"))->pluck("terminal_id"))->pluck("opening_id");
		} else {
			$openingIds = SalesOpening::whereBetween("date", [$request->first, $request->second])->where("terminal_id", $request->terminal)->pluck("opening_id");
		}

		return DB::table("sales_receipts")
			// ->when($request->type == "declaration" , function($query) use ($request,$openingIds){
			// $query->whereIn("sales_receipts.opening_id",$openingIds);
			// },function($q) use ($request){
			// $q->whereBetween("sales_receipts.date", [$request->first, $request->second]);
			// })
			->when($request->first != "" && $request->second != "" && $request->type == "declaration", function ($query) use ($request, $openingIds) {
				$query->whereIn("sales_receipts.opening_id", $openingIds);
				// },function($q) use ($request){
				// $q->whereBetween("sales_receipts.date", [$request->first, $request->second]);
			})

			->when($request->first != "" && $request->second != "" && $request->type == "datewise", function ($query) use ($request) {
				$query->whereBetween("sales_receipts.date", [$request->first, $request->second]);
			})
			->when($request->order_no != "", function ($query) use ($request) {
				$query->where('sales_receipts.id', $request->order_no);
			})
			->when($request->machineOrderNo != "", function ($query) use ($request) {
				$query->where('sales_receipts.machine_terminal_count', $request->machineOrderNo);
			})
			->when($request->branch != "" && $request->branch != "all", function ($query) use ($request) {
				$query->where('sales_receipts.branch', $request->branch);
			})
			->when($request->branch == "" && $request->branch != "all", function ($query) use ($request) {
				$query->where('sales_receipts.branch', session('branch'));
			})
			->when($request->branch == "all", function ($query) use ($request) {
				$query->whereIn('sales_receipts.branch', DB::table("branch")->where("company_id", session("company_id"))->pluck("branch_id"));
			})
			->when($request->terminal != "" && $request->branch != "all", function ($query) use ($request) {
				$query->where('sales_receipts.terminal_id', $request->terminal);
			})
			->when($request->payMode != "", function ($query) use ($request) {
				$query->where('sales_receipts.payment_id', $request->payMode);
			})
			->when($request->mode != "", function ($query) use ($request) {
				$query->where('sales_receipts.order_mode_id', $request->mode);
			})
			->when($request->receipt != "", function ($query) use ($request) {
				$query->where('sales_receipts.receipt_no', $request->receipt);
			})
			->when($request->customer != "", function ($query) use ($request) {
				$query->where('sales_receipts.customer_id', $request->customer);
			})
			->when($request->status != "", function ($query) use ($request) {
				$query->where('sales_receipts.status', $request->status);
			})
			->when($request->first != "", function ($query) use ($request) {
				$query->where("sales_receipts.date", '>=', $request->first);
			})
			->when($request->second != "", function ($query) use ($request) {
				$query->where("sales_receipts.date", '<=', $request->second);
			})
			->when($request->sales_tax != "", function ($query) use ($request) {
				$query->where("sales_receipts.fbrInvNumber", '!=', "");
			})
			->when($request->category != "" && $request->category != "all", function ($query) use ($request) {
				$query->where("sales_receipts.web", "=", $request->category);
			})
			// ->where("sales_receipts.web", "=", 0)
			->where("sales_receipts.void_receipt", "!=", 1)
			->selectRaw("COUNT(sales_receipts.id) as totalorders")
			->orderBy("sales_receipts.id", "desc")
			->groupBy("status")
			->get();
	}

	public function getTotalTax($request)
	{
		$fromDate = "";
		$toDate = "";
		if ($request->deli_from != "" && $request->deli_to != "") {
			$fromDate = $request->deli_from;
			$toDate = $request->deli_to;
		} else if ($request->first != "" && $request->second != "") {
			$fromDate = $request->first;
			$toDate = $request->second;
		}

		if (!empty($request->branch) && $request->branch[0] == "all" && !empty($request->terminal) && $request->terminal[0] == "all") {
			$openingIds = SalesOpening::whereBetween("date", [$fromDate, $toDate])->whereIn("terminal_id", DB::table("terminal_details")->whereIn("branch_id", DB::table("branch")->where("company_id", session("company_id"))->pluck("branch_id"))->pluck("terminal_id"))->pluck("opening_id");
		} else if (!empty($request->branch) &&  $request->branch[0] != "all" && !empty($request->terminal) && $request->terminal[0] == "all") {
			$openingIds = SalesOpening::whereBetween("date", [$fromDate, $toDate])->whereIn("terminal_id", DB::table("terminal_details")->whereIn("branch_id", DB::table("branch")->whereIn("branch_id", $request->branch)->pluck("branch_id"))->pluck("terminal_id"))->pluck("opening_id");
		} else {
			$openingIds = SalesOpening::whereBetween("date", [$fromDate, $toDate])->whereIn("terminal_id", $request->terminal)->pluck("opening_id");
		}

		$ids =  DB::table("sales_receipts")

			->when($request->first != "" && $request->second != "" && $request->type == "declaration", function ($query) use ($request, $openingIds) {
				$query->whereIn("sales_receipts.opening_id", $openingIds);
			})
			->when($request->first != "" && $request->second != "" && $request->type == "datewise", function ($query) use ($request) {
				$query->whereBetween("sales_receipts.date", [$request->first, $request->second]);
			})
			->when($request->deli_from != "" && $request->deli_to != "" && $request->type == "declaration", function ($query) use ($request, $openingIds) {
				$query->whereIn("sales_receipts.opening_id", $openingIds);
			})
			->when($request->deli_from != "" && $request->deli_to != "" && $request->type == "datewise", function ($query) use ($request) {
				// $query->whereBetween("sales_receipts.delivery_date", [date("Ymd",strtotime($request->deli_from)), date("Ymd",strtotime($request->deli_to))]);
				$query->whereBetween(DB::raw('DATE(sales_receipts.order_delivery_date)'), [$request->deli_from, $request->deli_to]);
			})
			->when($request->branch != "" && $request->branch != "all", function ($query) use ($request) {
				$query->where('sales_receipts.branch', $request->branch);
			})
			->when($request->branch == "" && $request->branch != "all", function ($query) use ($request) {
				$query->where('sales_receipts.branch', session('branch'));
			})
			->when($request->branch == "all", function ($query) use ($request) {
				$query->whereIn('sales_receipts.branch', DB::table("branch")->where("company_id", session("company_id"))->pluck("branch_id"));
			})
			->when($request->terminal != "" && $request->branch != "all", function ($query) use ($request) {
				$query->where('sales_receipts.terminal_id', $request->terminal);
			})
			->when($request->payMode != "", function ($query) use ($request) {
				$query->where('sales_receipts.payment_id', $request->payMode);
			})
			->when($request->mode != "", function ($query) use ($request) {
				$query->where('sales_receipts.order_mode_id', $request->mode);
			})
			->when($request->receipt != "", function ($query) use ($request) {
				$query->where('sales_receipts.receipt_no', $request->receipt);
			})
			->when($request->customer != "", function ($query) use ($request) {
				$query->where('sales_receipts.customer_id', $request->customer);
			})
			->when($request->status != "", function ($query) use ($request) {
				$query->where('sales_receipts.status', $request->status);
			})
			->when($request->first != "", function ($query) use ($request) {
				$query->where("sales_receipts.date", '>=', $request->first);
			})
			->when($request->second != "", function ($query) use ($request) {
				$query->where("sales_receipts.date", '<=', $request->second);
			})
			->when($request->sales_tax != "", function ($query) use ($request) {
				$query->where("sales_receipts.fbrInvNumber", '!=', "");
			})
			->when($request->category != "" && $request->category != "all", function ($query) use ($request) {
				$query->where("sales_receipts.web", "=", $request->category);
			})
			->pluck("id");
			// ->toSql();
			// return $ids;

		return DB::table("sales_account_subdetails")->whereIn("receipt_id", $ids)->selectRaw("COUNT(receipt_id) as totalorders,SUM(srb) as srbtaxamount,SUM(sales_tax_amount) as fbrtaxamount")->get();
	}

	public function getPOSOrders($first, $second, $status, $customer, $receipt, $mode, $branch, $terminal, $payMode)
	{
		return DB::table("sales_receipts")
			->join("branch", "branch.branch_id", "=", "sales_receipts.branch")
			->join("terminal_details", "terminal_details.terminal_id", "=", "sales_receipts.terminal_id")
			->join("customers", "customers.id", "=", "sales_receipts.customer_id")
			->join("sales_order_mode", "sales_order_mode.order_mode_id", "=", "sales_receipts.order_mode_id")
			->join("sales_order_status", "sales_order_status.order_status_id", "=", "sales_receipts.status")
			->join("sales_payment", "sales_payment.payment_id", "=", "sales_receipts.payment_id")
			->where("sales_receipts.branch", session("branch"))
			->where("sales_receipts.web", "=", 0)
			->select("sales_receipts.*", "sales_receipts.time", "branch.branch_name", "terminal_details.terminal_name", "customers.name", "sales_order_mode.order_mode", "sales_order_status.order_status_name", "sales_payment.payment_mode")
			->orderBy("sales_receipts.id", "desc")
			->paginate(10);
	}

	public function getPOSFilterOrders($first, $second, $status, $customer, $receipt, $mode, $branch, $terminal, $payMode, $orderNo)
	{
		if ($branch == "") {
			$branch = session("branch");
		}
		// DB::enableQueryLog();
		$data =  DB::table("sales_receipts")
			->join("branch", "branch.branch_id", "=", "sales_receipts.branch")
			->join("terminal_details", "terminal_details.terminal_id", "=", "sales_receipts.terminal_id")
			->join("customers", "customers.id", "=", "sales_receipts.customer_id")
			->join("sales_order_mode", "sales_order_mode.order_mode_id", "=", "sales_receipts.order_mode_id")
			->join("sales_order_status", "sales_order_status.order_status_id", "=", "sales_receipts.status")
			->join("sales_payment", "sales_payment.payment_id", "=", "sales_receipts.payment_id")
			->where(function ($query) use ($first, $second, $status, $customer, $receipt, $mode, $branch, $terminal, $payMode, $orderNo) {
				// Everything within this closure will be grouped together
				if (!empty($orderNo)) {
					$query->where('sales_receipts.id', '=', empty($orderNo) ? '' : $orderNo);
				}
				if (!empty($branch)) {
					$query->where("sales_receipts.branch", '=', $branch);
				}
				if (!empty($terminal)) {
					$query->where("sales_receipts.terminal_id", '=', empty($terminal) ? '' : $terminal);
				}
				if (!empty($payMode)) {
					$query->where("sales_receipts.payment_id", '=', empty($payMode) ? '' : $payMode);
				}
				if (!empty($customer)) {
					$query->where("sales_receipts.customer_id", '=', empty($customer) ? '' : $customer);
				}
				if (!empty($receipt)) {
					$query->where("sales_receipts.receipt_no", '=', empty($receipt) ? '' : $receipt);
				}
				if (!empty($status)) {
					$query->where("sales_receipts.status", '=', empty($status) ? '' : $status);
				}
				if (!empty($mode)) {
					$query->where("sales_receipts.order_mode_id", '=', empty($mode) ? '' : $mode);
				}
				// ->whereBetween('sales_receipts.date', [$first, $second]);
			});
		if (!empty($first)) {
			$data->where("sales_receipts.date", '>=', $first);
		}
		if (!empty($second)) {
			$data->where("sales_receipts.date", '<=', $second);
		}

		$result = $data->select("sales_receipts.*", "sales_receipts.time", "branch.branch_name", "terminal_details.terminal_name", "customers.name", "sales_order_mode.order_mode", "sales_order_status.order_status_name", "sales_payment.payment_mode")
			->orderBy("sales_receipts.id", "desc")
			->paginate(10);
		// print_r(DB::getQueryLog());exit;
		return $result;
	}

	//GEt WEb Orders parameters
	//$first,$second,$status,$customer,$receipt,$mode,$delFrom,$delTo,$branch,$terminal,$payMode
	public function getWebOrders($branch, $isSeen)
	{
		$filter = "";
		//        if ($branch == "")
		//        {
		//            $branch = session('branch');
		//        }
		/********************************** DELIVERY DATES FILTER **********************/
		//        if($delFrom != "")
		//        {
		//            if ($filter  == "") {
		//                $filter .= " and a.delivery_date BETWEEN '".$delFrom."' and '".$delTo."'";
		//            }else{
		//                $filter .= " and a.delivery_date BETWEEN '".$delFrom."' and '".$delTo."'";
		//            }
		//
		//        }
		/********************************** DATES **************************************/
		//        if($first != "")
		//        {
		//            if ($filter  == "") {
		//                $filter .= " and a.date BETWEEN '".$first."' and '".$second."'";
		//            }else{
		//                $filter .= " and a.date BETWEEN '".$first."' and '".$second."'";
		//            }
		//
		//        }
		/********************************** CUSTOMER **************************************/
		//        if($customer != "")
		//        {
		//            if ($filter  == "") {
		//                $filter .= " and a.customer_id = ".$customer."";
		//            }else{
		//                $filter .= " and a.customer_id = ".$customer."";
		//            }
		//
		//        }

		/********************************** RECEIPT **************************************/
		//        if($receipt != "")
		//        {
		//            if ($filter  == "") {
		//                $filter .= " and a.receipt_no = ".$receipt."";
		//            }else{
		//                $filter .= " and a.receipt_no = ".$receipt."";
		//            }
		//
		//        }
		//
		//
		//        if($branch != "")
		//        {
		//            if ($filter  == "") {
		//                $filter .= " and a.branch = ".$branch."";
		//            }else{
		//                $filter .= " and a.branch = ".$branch."";
		//            }
		//        }
		if (session('roleId') == 2) {
			$filter = " and a.branch IN (SELECT branch_id FROM branch where company_id = " . session("company_id") . " )";
		} elseif (session('roleId') == 18) {
			$filter = " and a.branch IN (" . $branch . ") ";
		} else {
			$filter = " and a.branch = " . session("branch") . " ";
		}


		if ($isSeen == 1) {
			$filter .= ' and a.isSeen=1 and DATE_FORMAT(a.date,"%Y-%m-%d") = "' . date('Y-m-d') . '"';
		}



		$result = DB::select('SELECT a.id,a.receipt_no,a.url_orderid,b.order_mode,c.name,c.address,a.total_amount,d.order_status_name,g.branch_name as branch,a.date,a.time,a.delivery_date,a.order_mode_id,a.status,c.mobile,e.receive_amount,f.payment_mode,a.isSeen,a.fbrInvNumber,h.address,h.landmark,a.delivery_area_name,i.delivery_charges_amount as delivery_charges,a.delivery_type from sales_receipts a
							INNER JOIN sales_order_mode b on b.order_mode_id = a.order_mode_id
							INNER JOIN customers c on c.id = a.customer_id
							INNER JOIN sales_order_status d on d.order_status_id = a.status
							INNER JOIN branch g on g.branch_id = a.branch
							INNER JOIN sales_account_general e on e.receipt_id = a.id
							INNER JOIN sales_payment f on f.payment_id = a.payment_id
							INNER JOIN customer_addresses h on h.id = a.cust_location_id
							INNER JOIN sales_account_subdetails i on i.receipt_id = a.id
                            where  a.web = 1 ' . $filter . '  order by a.id DESC');
		return $result;
	}





	public function getWebsiteOrders($branch)
	{
		$filter = "";
		// $branch = explode(',', $branch);
		//        if ($branch == "")
		//        {
		//            $branch = session('branch');
		//        }
		/********************************** DELIVERY DATES FILTER **********************/
		//        if($delFrom != "")
		//        {
		//            if ($filter  == "") {
		//                $filter .= " and a.delivery_date BETWEEN '".$delFrom."' and '".$delTo."'";
		//            }else{
		//                $filter .= " and a.delivery_date BETWEEN '".$delFrom."' and '".$delTo."'";
		//            }
		//
		//        }
		/********************************** DATES **************************************/
		//        if($first != "")
		//        {
		//            if ($filter  == "") {
		//                $filter .= " and a.date BETWEEN '".$first."' and '".$second."'";
		//            }else{
		//                $filter .= " and a.date BETWEEN '".$first."' and '".$second."'";
		//            }
		//
		//        }
		/********************************** CUSTOMER **************************************/
		//        if($customer != "")
		//        {
		//            if ($filter  == "") {
		//                $filter .= " and a.customer_id = ".$customer."";
		//            }else{
		//                $filter .= " and a.customer_id = ".$customer."";
		//            }
		//
		//        }

		/********************************** RECEIPT **************************************/
		//        if($receipt != "")
		//        {
		//            if ($filter  == "") {
		//                $filter .= " and a.receipt_no = ".$receipt."";
		//            }else{
		//                $filter .= " and a.receipt_no = ".$receipt."";
		//            }
		//
		//        }
		//
		//
		//        if($branch != "")
		//        {
		//            if ($filter  == "") {
		//                $filter .= " and a.branch = ".$branch."";
		//            }else{
		//                $filter .= " and a.branch = ".$branch."";
		//            }
		//        }
		if (session('roleId') == 2) {
			$filter = " and a.branch IN (SELECT branch_id FROM branch where company_id = " . session("company_id") . " )";
		} else {
			$filter = " and a.branch IN(" . $branch . ") ";
		}





		$result = DB::select('SELECT a.id,a.receipt_no,a.url_orderid,b.order_mode,c.name,a.total_amount,d.order_status_name,g.branch_name as branch,a.date,a.delivery_date,a.order_mode_id,a.status,c.mobile,e.receive_amount, f.payment_mode,a.isSeen,a.fbrInvNumber,c_add.address,c_add.landmark,a.delivery_area_name,a.delivery_charges,a.delivery_type from sales_receipts a
							INNER JOIN sales_order_mode b on b.order_mode_id = a.order_mode_id
							INNER JOIN customers c on c.id = a.customer_id
							INNER JOIN customer_addresses c_add on c_add.id = a.cust_location_id
							INNER JOIN sales_order_status d on d.order_status_id = a.status
							INNER JOIN branch g on g.branch_id = a.branch
							INNER JOIN sales_account_general e on e.receipt_id = a.id
							INNER JOIN sales_payment f on f.payment_id = a.payment_id
                            where  a.web = 1 ' . $filter . '  order by a.id DESC');
		return $result;
	}

	public function getWebsiteOrdersFilter($first, $second, $customer, $receipt, $branch, $websiteId)
	{
		$filter = "";

		/********************************** DATES **************************************/
		if (!empty($first)) {
			$filter .= " and a.date BETWEEN '" . $first . "' and '" . $second . "'";
		}
		/********************************** CUSTOMER **************************************/
		if (!empty($customer)) {
			$filter .= " and a.customer_id = " . $customer . "";
		}

		/********************************** RECEIPT **************************************/
		if (!empty($receipt)) {
			$filter .= " and a.url_orderid = '" . $receipt . "'";
		}

		if (!empty($branch)) {
			$filter .= " and a.branch = " . $branch . "";
		}


		if (!empty($websiteId)) {
			$filter .= " and a.website_id = " . $websiteId . "";
		}



		$result = DB::select('SELECT a.id,a.receipt_no,a.url_orderid,b.order_mode,c.name,c.address,a.total_amount,d.order_status_name,g.branch_name as branch,a.date,a.time,a.delivery_date,a.order_mode_id,a.status,c.mobile,e.receive_amount, f.payment_mode,a.fbrInvNumber,a.isSeen,c_add.address,c_add.landmark,a.delivery_area_name,a.delivery_charges,a.delivery_type from sales_receipts a
							INNER JOIN sales_order_mode b on b.order_mode_id = a.order_mode_id
							INNER JOIN customers c on c.id = a.customer_id
							INNER JOIN customer_addresses c_add on c_add.id = a.cust_location_id
							INNER JOIN sales_order_status d on d.order_status_id = a.status
							INNER JOIN branch g on g.branch_id = a.branch
							INNER JOIN sales_account_general e on e.receipt_id = a.id
							INNER JOIN sales_payment f on f.payment_id = a.payment_id
                            where  a.web = 1 ' . $filter . '  order by a.id DESC');
		return $result;
	}

	public function getWebOrdersFilter($first, $second, $customer, $receipt, $branch)
	{
		$filter = "";

		/********************************** DATES **************************************/
		if ($first != "") {
			if ($filter  == "") {
				$filter .= " and a.date BETWEEN '" . $first . "' and '" . $second . "'";
			}
		}
		/********************************** CUSTOMER **************************************/
		if ($customer != "") {
			if ($filter  == "") {
				$filter .= " and a.customer_id = " . $customer . "";
			}
		}

		/********************************** RECEIPT **************************************/
		if ($receipt != "") {
			if ($filter  == "") {
				$filter .= " and a.receipt_no = " . $receipt . "";
			}
		}

		if ($branch != "") {
			if ($filter  == "") {
				$filter .= " and a.branch = " . $branch . "";
			}
		}

		$result = DB::select('SELECT a.id,a.receipt_no,b.order_mode,c.name,c.address,a.total_amount,d.order_status_name,g.branch_name as branch,a.date,a.delivery_date,a.order_mode_id,a.status,c.mobile,e.receive_amount, f.payment_mode,a.fbrInvNumber,a.isSeen from sales_receipts a
							INNER JOIN sales_order_mode b on b.order_mode_id = a.order_mode_id
							INNER JOIN customers c on c.id = a.customer_id
							INNER JOIN sales_order_status d on d.order_status_id = a.status
							INNER JOIN branch g on g.branch_id = a.branch
							INNER JOIN sales_account_general e on e.receipt_id = a.id
							INNER JOIN sales_payment f on f.payment_id = a.payment_id
                            where  a.web = 1 ' . $filter . '  order by a.id DESC');
		return $result;
	}

	public function web_onlineOrderDetails($id)
	{
		$filter = "";
		//if (session('roleId') == 2) {
		// $filter = " and sales_receipts.branch IN (SELECT branch_id FROM branch where company_id = " . session("company_id") . " )";
		// } else {
		// 	$filter = " and sales_receipts.branch = " . session("branch") . " ";
		// }

		return DB::table('sales_receipts')
			->join('sales_account_subdetails', 'sales_account_subdetails.receipt_id', 'sales_receipts.id')
			->join('sales_order_status', 'sales_order_status.order_status_id', 'sales_receipts.status')
			->join('branch', 'branch.branch_id', 'sales_receipts.branch')
			->join('company', 'company.company_id', 'branch.company_id')
			->join('website_details', 'website_details.id', 'sales_receipts.website_id')
			->select('sales_receipts.*', 'sales_order_status.order_status_name as status_name', 'website_details.name as website_name', 'website_details.type as website_type', 'website_details.order_estimate_time', 'sales_account_subdetails.discount_amount', 'sales_account_subdetails.discount_percentage', 'branch.branch_name', 'company.name as company_name')
			->whereRaw('sales_receipts.url_orderid = "' . $id . '" ' . $filter)
			->get();
	}

	public function orderStatus()
	{
		$result = DB::table('sales_order_status')->orderBy("sortBy", "asc")->get();
		return $result;
	}

	public function paymentMode()
	{
		$result = DB::table('sales_payment')->get();
		return $result;
	}

	public function ordersMode()
	{
		$result = DB::table('sales_order_mode')->get();
		return $result;
	}

	public function getCustomers()
	{
		$result = DB::table('customers')->join("user_authorization", "user_authorization.user_id", "=", "customers.user_id")->where("user_authorization.branch_id", session("branch"))->distinct("customers.id")->get();
		return $result;
	}

	public function getWebsiteCustomers()
	{
		$result = DB::table('customers')->whereIn("website_id", DB::table('website_details')->where('company_id', session('company_id'))->where('status', 1)->pluck('id'))->get();
		return $result;
	}

	public function orderItems($orderID)
	{
		$result = DB::table('sales_receipt_details')
			->join('inventory_general', 'inventory_general.id', '=', 'sales_receipt_details.item_code')
			->where('sales_receipt_details.receipt_id', $orderID)
			->select('inventory_general.id', 'inventory_general.product_name', 'sales_receipt_details.status')
			->get();
		return $result;
	}

	public function orderItemsForPrint($orderID)
	{
		$result = DB::table('sales_receipt_details')
			->join('inventory_general', 'inventory_general.id', '=', 'sales_receipt_details.item_code')
			->where('sales_receipt_details.receipt_id', $orderID)
			->select('inventory_general.id', 'inventory_general.product_name', 'sales_receipt_details.*')
			->get();
		return $result;
	}

	public function chkOrder($finished, $product, $receipt)
	{
		$result = DB::table('master_assign')->where(['finished_good_id' => $finished, 'product_id' => $product, 'receipt_no' => $receipt])->count();
		return $result;
	}

	//Assign General Table
	public function insertAssign($items)
	{
		$result = DB::table('master_assign')->insertGetId($items);
		return $result;
	}

	//Assign Sub Table
	public function insertSubAssign($items)
	{
		if (DB::table('master_assign_details')->insert($items)) {
			return 1;
		} else {
			return 0;
		}
	}

	public function getReceiptGeneral($receipt_id)
	{

		$result = DB::select('SELECT a.id as receiptID,a.receipt_no,b.order_mode,c.id as customerId,c.name as customerName,c.mobile,c.phone,c.address,a.total_amount,a.actual_amount,d.order_status_name,g.branch_name as branch,g.branch_id as branchId,h.terminal_name,a.date,a.time,c.mobile,e.receive_amount, f.payment_mode,i.*,y.*,z.* from sales_receipts a
							INNER JOIN sales_order_mode b on b.order_mode_id = a.order_mode_id
							LEFt JOIN customers c on c.id = a.customer_id
							INNER JOIN sales_order_status d on d.order_status_id = a.status
							INNER JOIN branch g on g.branch_id = a.branch
							LEFT JOIN terminal_details h on h.terminal_id = a.terminal_id
							INNER JOIN sales_account_general e on e.receipt_id = a.id
							INNER JOIN sales_payment f on f.payment_id = a.payment_id
							INNER JOIN sales_account_subdetails i on i.receipt_id = a.id
                            LEFT JOin delivery_charges y on y.id = i.delivery_charges
                            LEFT JOIN taxes z on z.id = i.credit_card_transaction
                            where a.receipt_no =  ?', [$receipt_id]);

		return $result;
	}

	public function getCustBalance($id)
	{
		$result = DB::select("SELECT balance FROM `customer_account` where cust_id = ? and cust_account_id = (select MAX(cust_account_id) from customer_account where cust_id = ?)", [$id, $id]);
		return $result;
	}

	public function updateAssign($items, $id)
	{
		if (DB::table('master_assign_temporary')->where('sub_assign_id', $id)->update($items)) {
			return 1;
		} else {
			return 0;
		}
	}

	public function chkMasterTemp($product)
	{
		$result = DB::table('master_assign_temporary')->where(['product_id' => $product])->count();
		return $result;
	}

	public function masterAssignTemp($items)
	{
		if (DB::table('master_assign_temporary')->insert($items)) {
			return 1;
		} else {
			return 0;
		}
	}

	public function deletemasterAssignTemp()
	{
		$result = DB::select('Delete from master_assign_temporary');
		return $result;
	}

	public function getTempItems()
	{
		$result = DB::table('master_assign_temporary')
			->join('inventory_general', 'inventory_general.id', '=', 'master_assign_temporary.product_id')
			->join('inventory_uom', 'inventory_uom.uom_id', '=', 'master_assign_temporary.uom_id')
			->get();
		return $result;
	}

	public function getTempDetails($id)
	{
		$result = DB::table('master_assign_details')
			->join('inventory_general', 'inventory_general.id', '=', 'master_assign_details.product_id')
			->join('inventory_uom', 'inventory_uom.uom_id', '=', 'master_assign_details.uom_id')
			->where('master_assign_details.assign_id', $id)
			->get();
		return $result;
	}

	public function getTempItemsForInsert()
	{
		$result = DB::table('master_assign_temporary')->get();
		return $result;
	}

	public function getItems($finished, $receipt)
	{
		$result = DB::table('master_assign')
			->join('inventory_general', 'inventory_general.id', '=', 'master_assign.product_id')
			->join('inventory_uom', 'inventory_uom.uom_id', '=', 'master_assign.uom_id')
			->where(['finished_good_id' => $finished, 'receipt_no' => $receipt])
			->get();
		return $result;
	}

	public function updateItemStatus($finished, $receipt, $status)
	{
		$id = DB::table('sales_receipt_details')->where(['receipt_id' => $receipt, 'item_code' => $finished])->get();

		if (DB::table('sales_receipt_details')->where('receipt_detail_id', $id[0]->receipt_detail_id)->update(['status' => $status])) {
			return 1;
		} else {
			return 0;
		}
	}

	public function getItemsQty($receipt, $code)
	{
		$result = DB::select('SELECT total_qty - IFNULL((Select SUM(qty) from master_assign where finished_good_id = ? and receipt_no = ?),0) as assignQty FROM `sales_receipt_details` where receipt_id = ? and item_code = ?', [$code, $receipt, $receipt, $code]);
		return $result;
	}

	public function getAssignItems($receipt)
	{
		$result = DB::select('SELECT a.*,b.product_name as product,c.name as master FROM master_assign a INNER JOIN inventory_general b on b.id = a.finished_good_id INNER JOIN masters c on c.id = a.master_id where a.receipt_no = ? group by a.finished_good_id,a.master_id', [$receipt]);
		return $result;
	}

	public function getMasterRate($finished, $master, $receipt)
	{
		$result = DB::select('SELECT total_qty * (Select rate from master_category_rate where finished_good_id = ? and master_id = ?) as rate FROM `sales_receipt_details` where receipt_id = ? and item_code = ?', [$finished, $master, $receipt, $finished]);
		return $result;
	}

	public function insertIntoMaster($items)
	{
		if (DB::table('master_account')->insert($items)) {
			return 1;
		} else {
			return 0;
		}
	}

	public function getItemsForDeduction($finished, $receipt)
	{
		$result = DB::select('SELECT * FROM master_assign where finished_good_id = ? and receipt_no = ?', [$finished, $receipt]);
		return $result;
	}

	public function getReceiptItems($receipt)
	{
		$result = DB::select('SELECT b.product_name,a.total_qty,a.total_amount FROM sales_receipt_details a
				INNER JOIN inventory_general b on b.id = a.item_code
				where a.receipt_id =  ?', [$receipt]);
		return $result;
	}

	public function compareStatus($receipt)
	{
		$result = DB::select('SELECT COUNT(a.item_code) as count,(SELECT COUNT(a.item_code) from sales_receipt_details a where a.receipt_id = ?) as masterAssign from sales_receipt_details a where a.receipt_id = ? and a.status = 2', [$receipt, $receipt]);
		return $result;
	}

	public function updateSalesReceiptStatus($id)
	{
		if (DB::table("sales_receipts")->where('id', $id)->update(['status' => 2])) {
			return 1;
		} else {
			return 0;
		}
	}

	public function getMasterByCategory($id)
	{
		$result = DB::select("SELECT * FROM masters where id IN(SELECT master_id FROM master_category_rate where finished_good_id = ?)", [$id]);
		return $result;
	}

	public function masterOrderCount($master)
	{
		$result = DB::select("SELECT COUNT(assign_id) as orderCount FROM master_assign where master_id = ? and status = 2", [$master]);
		return $result;
	}

	public function getBranch()
	{
		if (session('roleId') == 2 or session('roleId') == 17 or session('roleId') == 19 or session('roleId') == 20) {
			$result = DB::table('branch')->where('company_id', session('company_id'))->get();
		} else {
			$result = DB::table('branch')->where('branch_id', session('branch'))->get();
		}

		return $result;
	}

	public function getTerminal($branch)
	{
		$result = DB::table('terminal_details')->where('branch_id', $branch)->get();
		return $result;
	}

	public function updateBranchOrder($id, $branch)
	{
		if (DB::table('sales_receipts')->where('id', $id)->update(["branch" => $branch])) {
			return 1;
		} else {
			return 0;
		}
	}

	public function updateStatusOrder($id, $status, $rider)
	{
		$items = [
			"status" => $status,
			"rider_id" => ($rider != 0 ? $rider : 0),
		];
		if (DB::table('sales_receipts')->where('id', $id)->update($items)) {
			DB::table("sales_online_order_status")->insert(["order_id" => $id, "status_id" => $status, "date" => date("Y-m-d"), "time" => date("H:i:s"), "user_id" => auth()->user()->id]);
			return 1;
		} else {
			return 0;
		}
	}

	public function sentToWorkshop($itemId)
	{
		$items = [
			"mode" => "worker",
		];
		if (DB::table('sales_receipt_details')->where('receipt_detail_id', $itemId)->update($items)) {
			return 1;
		} else {
			return 0;
		}
	}

	public function changeItemStatus($id, $itemId, $status)
	{
		$items = [
			"status" => $status,
		];
		if (DB::table('sales_receipt_details')->where('receipt_detail_id', $itemId)->update($items)) {
			DB::table("sales_online_item_status")->insert(["order_id" => $id, "item_id" => $itemId, "status_id" => $status, "branch_id" => session('branch'), "user_id" => auth()->user()->id, "date" => date("Y-m-d"), "time" => date("H:i:s")]);
			return 1;
		} else {
			return 0;
		}
	}



	public function updateOrderStatusWithLogs($id, $status, $name, $mobile, $comments, $branch)
	{
		if (DB::table('sales_receipts')->where('id', $id)->update(["status" => $status])) {
			if (DB::table("sales_online_order_status")->insert(["order_id" => $id, "status_id" => $status, "branch_id" => $branch, "name" => $name, "mobile" => $mobile, "comments" => $comments, "date" => date("Y-m-d"), "time" => date("H:i:s")])) {
				return 1;
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}

	public function getRiders()
	{
		return DB::table("service_provider_details")->where("branch_id", session("branch"))->where("categor_id", 1)->where("status_id", 1)->get();
	}

	
}

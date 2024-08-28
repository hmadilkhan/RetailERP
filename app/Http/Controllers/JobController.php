<?php

namespace App\Http\Controllers;

use App\joborder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\Controller;


class JobController extends Controller
{
    public function getList(Request $request,joborder $joborder)
    {
		if(auth()->user()->company->application_id == 2){
			$result = $joborder->getRestaurantList();
		}else{
			$result = $joborder->getList(1);
		}
        
        return view('JobOrder.view-joborders', compact('result'));
    }

    public function create(Request $request,joborder $joborder)
    {
        $products = $joborder->getfinishgoods();
        $raw = $joborder->getproducts();
        return view("JobOrder.create-joborders",compact('products','raw'));
    }

    public function getRaw(Request $request,joborder $joborder)
    {
        $raw = $joborder->getProductByID($request->id);
        return $raw;
    }

    public function addJob(Request $request,joborder $joborder)
    {

        if($request->jobid == "")
        {
            $items = [
                'product_id' => $request->id,
                'branch_id' => session("branch"),
                'wastage' => 0,
                'status_id' => 1, //by default active
                
            ];
            $general = $joborder->insert('recipy_general',$items);
            return $general;
        }
    }

    public function addJobDetails(Request $request,joborder $joborder)
    {
        $count = $joborder->getJobCount($request->id,$request->itemid);

        if ($count == 0)
        {
            $details = [
                'recipy_id' => $request->id,
                'item_id' => $request->itemid,
                'mode_id' => $request->productmode,
                'usage_qty' => $request->usage,
                'cost' => $request->amount,
				'used_in_dinein' => $request->dinein, //by default active
            ];
            $Itemdetails = $joborder->insert_sub_details($details);
        }
        else
        {
            return 2;
        }

    }

    public function getJobData(Request $request,joborder $joborder)
    {
        $Itemdetails = $joborder->loadJob($request->jobid);
        return $Itemdetails;
    }

    public function getCost(Request $request,joborder $joborder)
    {
        $cost = $joborder->getCost($request->jobid);
        return $cost[0]->amount;
    }

    public function ItemUpdate(Request $request,joborder $joborder)
    {
        $details = [
            'item_id' => $request->itemid,
            'mode_id' => $request->productmode,
            'usage_qty' => $request->usage,
            'cost' => $request->amount,
            'used_in_dinein' => $request->dineIn,
        ];

        $Itemdetails = $joborder->UpdateJobSubDetails($request->updateid,$details);
        return $Itemdetails;
    }

    public function ItemDelete(Request $request,joborder $joborder)
    {
        $Itemdetails = $joborder->DeleteItem($request->id,$request->recipyid);
        return $Itemdetails;
    }

    public function accountAdd(Request $request,joborder $joborder)
    {
        $account = [
            'recipy_id' => $request->jobid,
            'ingredients_cost' => $request->ic,
            'material_cost' => $request->pc,
            'total_cost' => $request->totalcost,
            'infrastructure_cost' => $request->infra,
        ];

        $Itemdetails = $joborder->accountAdd($account);
        return $Itemdetails;

    }

    public function accountUpdate(Request $request,joborder $joborder)
    {
        $account = [
            'recipy_id' => $request->recipyid,
            'ingredients_cost' => $request->ic,
            'material_cost' => $request->pc,
            'total_cost' => $request->total,
            'infrastructure_cost' => $request->infra,
        ];

        $Itemdetails = $joborder->accountUpdate($request->recipyid,$account);
        return $Itemdetails;

    }

    public function ReceivedProduct(Request $request, joborder $joborder)
    {

        $result = $joborder->ReceivedProduct();
        $items = [
            'GRN' => $result,
            'job_id' =>$request->jobid,
            'item_id' => $request->itemid,
            'qty_rec' => $request->recivedqty,
            'status_id' => 3,
        ];
        return $items;

        $received = $joborder->ReceivedProductDetails($items);

        $jobAccount = $joborder->$request->jobid;
        $cost = $jobAccount[0]->cost + $jobAccount[0]->infrastructure_cost;
        $stock = [
            'grn_id' => $result,
            'product_id' => $request->itemid,
            'uom' => $request->itemid,
            'cost_price' => $cost,
            'retail_price' => $jobAccount[0]->retail_cost,
            'wholesale_price' => 0,
            'discount_price' => 0,
            'qty' => $request->recivedqty,
            'balance' => $request->recivedqty,
            'status_id' => 1,
            'branch_id' => session("branch"),
        ];

        $stock = $joborder->Stock($stock);

        $JobOrderGeneralUpdate = $joborder->updateJobOrderGeneral($request->jobid,$request->recivedqty);
        return 1;
    }

    public function edit(Request $request,joborder $joborder)
    {
        $products = $joborder->getfinishgoods();
        $raw = $joborder->getproducts();
         $details = $joborder->getdetailsRecipy(Crypt::decrypt($request->id));
        $general = $joborder->recipy_general(Crypt::decrypt($request->id));
        return view("JobOrder.edit-joborders",compact('products','raw','general','details'));
    }

    public function RepeatJobOrder(Request $request,joborder $joborder)
    {
        $products = $joborder->getfinishgoods();
//        $raw = $joborder->getRaw();
//        $uom = $joborder->getuom();
        return view("WorkOrder.create-workorder",compact('products'));
    }

    public function getJobIdFromProduct(Request $request,joborder $joborder)
    {
        $result = $joborder->GetDataFromRecipyWithID($request->productId);
        return $result;
    }

    public function getJobDataFromID(Request $request,joborder $joborder)
    {
        $TempData = $joborder->GetDataFromRecipyWithID($request->id);
        return $TempData;
    }

    public function TempUpdate(Request $request,joborder $joborder)
    {
        $details = [
            'usage_qty' => $request->usage,
            'amount' => $request->amount,
        ];

        $Itemdetails = $joborder->UpdateTempSubDetails($request->updateid,$details);
        return $Itemdetails;
    }

    public function getTempData(Request $request,joborder $joborder)
    {
        $TempData = $joborder->GetDataFromTemp();
        return $TempData;
    }

    public function InsertIntoTemp(Request $request,joborder $joborder)
    {
        $count = $joborder->getProductCount($request->item_id);
        if($count == 0)
        {
            $items = [
                'item_id' => $request->item_id,
                'usage_qty' => $request->usage_qty,
                'amount' => $request->amount,
            ];
            $TempData = $joborder->InsertIntoTempData($items);
            return $TempData;
        }
        else
        {
            return 2;
        }

    }

    public function getTempCost(Request $request,joborder $joborder)
    {
        $cost = $joborder->getTempCost();
        return $cost;
    }

    public function TempItemDelete(Request $request,joborder $joborder)
    {
        $Itemdetails = $joborder->DeleteTempItem($request->id);
        return $Itemdetails;
    }

    public function getJobDetails(Request $request,joborder $joborder)
    {
        $result = $joborder->getDetails();
        return view("WorkOrder.list-workorder",compact("result"));
    }

    public function jobCancel(Request $request,joborder $joborder)
    {
        $Itemdetails = $joborder->cancelJob($request->id);
        return $Itemdetails;
    }

    public function jobCost(Request $request,joborder $joborder)
    {
        $Itemdetails = $joborder->jobCost($request->id);
        return $Itemdetails;

    }

    public function jobSubmit(Request $request,joborder $joborder)
    {

        if ($request->workorderid == 0) {

            $items = [
                'joborder_name' => $request->workordername,
                'job_status_id' => 2,
                'branch_id' => session("branch"),
            ];
            $general = $joborder->insert('job_order_general',$items);

            $items = [
                'product_id' => $request->product,
                'order_qty' => $request->qty,
                'job_order_id' => $general,
                'job_cost' => $request->cost,
                'rp_cost' => 0,
            ];

            $subdetails = $joborder->insert('job_order_subdetails',$items);

            return $general;

        }
        else{
            $count = $joborder->sub_exsits($request->product,$request->workorderid);
            if ($count[0]->counts == 0) {
                $items = [
                    'product_id' => $request->product,
                    'order_qty' => $request->qty,
                    'job_order_id' => $request->workorderid,
                    'job_cost' => $request->cost,
                    'rp_cost' => 0,
                ];
                $subdetails = $joborder->insert('job_order_subdetails',$items);
                return $request->workorderid;
            }
            else{
                return 0;
            }

        }

    }

    public function getorderdetails(Request $request,joborder $joborder)
    {
        $result = $joborder->order_details($request->workorderid);
        return $result;
    }

    public function getorderdetailsSUM(Request $request,joborder $joborder)
    {
        $result = $joborder->getsum($request->workorderid);
        return $result;
    }

    public function accsubmit(Request $request,joborder $joborder)
    {
        // job order account insert start

        $items = [
            'job_id' => $request->workorderid,
            'cost' => $request->totalcost,
            'retail_cost' => 0,
        ];

        $orderaccounts = $joborder->insert('job_order_account',$items);


        // job order account insert end

        // GRN for production insert start

        $result = $joborder->order_details($request->workorderid);

        $grnid = 0;
        foreach ($result as  $val) {
            $exsit = $joborder->getgrncount($request->workorderid);
            if ($exsit[0]->counts == 0) {
                //general GRN insert here
                $grnid = $joborder->ReceivedProduct();
                $items = [
                    'GRN'=>$grnid,
                    'job_id'=>$request->workorderid,
                    'item_id'=>$val->product_id,
                    'qty_rec'=>$val->order_qty,
                    'status_id'=>3,
                ];
                //details of GRN insert here
                $received = $joborder->ReceivedProductDetails($items);
            }
            else{

                $items = [
                    'GRN'=>$grnid,
                    'job_id'=>$request->workorderid,
                    'item_id'=>$val->product_id,
                    'qty_rec'=>$val->order_qty,
                    'status_id'=>3,
                ];
                //details of GRN insert here
                $received = $joborder->ReceivedProductDetails($items);

            }


            // GRN for production insert end

            // get unit of measure
            $uom = $joborder->getuombyid($val->product_id);

            // get job cost and retail price
            $jobAccount = $joborder->getJobAccount($request->workorderid,$val->product_id);

            // foreach ($jobAccount as $value) {

            // inventory stock insert start
            $stock = [
                'grn_id' => $grnid,
                'product_id' => $val->product_id,
                'uom' => $uom[0]->uom_id,
                'cost_price' => $jobAccount[0]->job_cost,
                'retail_price' => $jobAccount[0]->rp_cost,
                'wholesale_price' => 0,
                'discount_price' => 0,
                'qty' => $val->order_qty,
                'balance' => $val->order_qty,
                'status_id' => 1,
                'branch_id' => session("branch"),
            ];

            $stock = $joborder->Stock($stock);
            // inventory stock insert end
            // }

            // raw material deduction start
            $recipyDetails = $joborder->getRecipyDetails($val->product_id);


            foreach ($recipyDetails as $value) {

                $rawQty = $joborder->getRawStock($value->item_id);
                $recipyQty = $value->usage_qty * $val->order_qty;
                $totalQty = $rawQty[0]->qty - ((($rawQty[0]->qty * $rawQty[0]->weight_qty)  - $recipyQty) / $rawQty[0]->weight_qty);

                $deduction = $joborder->invent_stock_detection(session('branch'),$value->item_id,$totalQty);
            }
            // raw material deduction end
        }

        return $orderaccounts;
        // return job order account id for sweet alert
    }

    public function orderqty_update(Request $request,joborder $joborder)
    {
        $result = $joborder->qty_update($request->tableid, $request->qty);
        return $result;
    }
    public function orderdetails_delete(Request $request,joborder $joborder)
    {
        $count = $joborder->getcount($request->workorderid);
        if ($count[0]->counts == 1) {

            $result = $joborder->item_delete($request->tableid);

            $orderdelete = $joborder->complete_delete($request->workorderid);
            return $orderdelete;
        }
        else{
            $result = $joborder->item_delete($request->tableid);
            return $result;
        }

    }





    public function getrecipyCalculation(Request $request,joborder $joborder)
    {
        $result = $joborder->recipyCalculation($request->id);
        return $result;
    }

    public function chk_recipy_exists(Request $request,joborder $joborder)
    {
        $result = $joborder->chk_already_recipy($request->id);
        return $result;
    }

    public function getdetails(Request $request,joborder $joborder)
    {
        $details = $joborder->getjoborderdetails(Crypt::decrypt($request->id));
        return view('JobOrder.details-joborders', compact('details'));
    }


    public function deletejoborder(Request $request,joborder $joborder)
    {
        $result = $joborder->Deleteall($request->jobid);
        return $result;
    }

    public function workorderdetails(Request $request,joborder $joborder)
    {
        $details = $joborder->workorderdetails(Crypt::decrypt($request->id));
        $sum = $joborder->workorderdetails_sum(Crypt::decrypt($request->id));
        return view('WorkOrder.details-workorder', compact('details','sum'));
    }

    public function getunitofmeassure(Request $request,joborder $joborder)
    {
        $uom = $joborder->getuom($request->productid);
        return $uom;

    }

    public function getList_inactive(Request $request,joborder $joborder)
    {
        $result = $joborder->getList(2);
        return $result;
    }

    public function createagain(Request $request,joborder $joborder)
    {
        $recipyid = $joborder->getrecid($request->id);
        return $recipyid;
    }


    public function inactiveoldecipy(Request $request,joborder $joborder)
    {
        $items = [
            'status_id' => 2, //in active
        ];
        $result = $joborder->inactiveoldecipy($request->recipyid,$items);
        return $result;
    }

    public function reactiverecipy(Request $request,joborder $joborder)
    {
        $count = $joborder->getrecipycount($request->productid);
        if ($count[0]->counts == 0)
        {
            $items = [
                'status_id' => 1, //re active
            ];
            $result = $joborder->inactiveoldecipy($request->recipyid,$items);
            return 1;
        }
        else{
            return 0;
        }

    }
    public function inactiverecipy(Request $request,joborder $joborder)
    {
        $items = [
            'status_id' => 2, //in active
        ];
        $result = $joborder->inactiveoldecipy($request->recipyid,$items);
        return $result;
    }




}
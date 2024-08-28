<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\receiveddemand;
use App\demand;


class ReceiveddemandController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(receiveddemand $recdemand)
    {
        $demands = $recdemand->get_demandslist();

        return view('Received-Demands.received-demand', compact('demands'));
    }
    public function show(Request $request, receiveddemand $recdemand, demand $demand)
    {


        $details = $demand->demand_details_show($request->id);
        $sender = $demand->get_sender_info($request->id);
        $reciver = $demand->get_reciver_info();
        $status = $recdemand->status();
        $branches = $recdemand->branches();


        return view('Received-Demands.received-demandpanel', compact('details', 'sender', 'reciver', 'status', 'branches'));
    }

    public function update_status(Request $request, receiveddemand $recdemand)
    {

        $status = $recdemand->update_status($request->id, $request->statusid);
        return $status;
    }

    public function updatedemanditem(Request $request, receiveddemand $recdemand)
    {
        $result = $recdemand->updateitem_demand($request->id, $request->statusid);
        return $result;
    }

    public function getstock(Request $request, receiveddemand $recdemand)
    {
        $stock = $recdemand->stock_details($request->itemcode, '');
        return $stock;
    }

    public function check(Request $request, receiveddemand $recdemand)
    {
        $stock = $recdemand->stock_details($request->itemcode, $request->branchfrom);

        if (count($stock) > 0) {
            if ($request->qty  > $stock[0]->stock || $request->qty <= 0) {
                return 1;
            } else {

                return 0;
            }
        }
    }

    public function insert(Request $request, receiveddemand $recdemand)
    {

        $exsitschk = $recdemand->exsits_chk($request->branchfrom, $request->demandid);

        if ($exsitschk[0]->doid == 0) {


            $count = $recdemand->get_count();
            $count = $count + 1;


            $items = [

                'transfer_No' => $count,
                'demand_id' => $request->demandid,
                'user_id' => session('userid'),
                'status_id' => 4,
                'date' => date('Y-m-d'),
                'time' => date('H:s:i'),
                'branch_from' => $request->branchfrom,
                'branch_to' => $request->branchto,

            ];
            $addtransfer = $recdemand->insert_transfer('transfer_general_details', $items);

            $items = [
                'transfer_id' => $addtransfer,
                'product_id' => $request->productid,
                'cp' => 'NULL',
                'qty' => $request->qty,
                'status_id' => 4,
            ];
            $additems = $recdemand->insert_transfer('transfer_item_details', $items);
            return $additems;
        } else {

            $transferid = $recdemand->gettransferid($request->demandid, $request->branchfrom);

            $items = [
                'transfer_id' => $transferid[0]->transfer_id,
                'product_id' => $request->productid,
                'cp' => 'NULL',
                'qty' => $request->qty,
                'status_id' => 4,
            ];
            $additems = $recdemand->insert_transfer('transfer_item_details', $items);
            return $additems;
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\sms;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class SMSController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function view(sms $sms)
    {
        $general = $sms->getsmsgeneral();
        $sub = $sms->getsmssubdetails();
        return view('SMS.sms-panel', compact('general', 'sub'));
    }

    public function store(sms $sms, Request $request)
    {

        $items = [
            'name' => $request->sendername,
            'message' => '',
            'status_id' => 1,
        ];

        $general = $sms->insert('sms_general_details', $items);

        $mobile = $request->mobile_number;
        $mobile = explode(",", $mobile);

        for ($i = 0; $i < count($mobile); $i++) {

            $sms->insert_sms_subdetails(['sms_id' => $general, 'mobile_number' => $mobile[$i], 'status_id' => 1]);
        }

        return 1;
    }


    public function getdetails(sms $sms, request $request)
    {
        $details = $sms->getsmssubdetailsbyid($request->id);
        return $details;
    }

    public function update(sms $sms, Request $request)
    {

        $items = [
            'mobile_number' => $request->mobile_number,
        ];
        $result = $sms->update_subdetails($request->id, $items);
        return $result;
    }

    public function updategeneral(sms $sms, Request $request)
    {

        $items = [
            'name' => $request->name,
        ];
        $result = $sms->update_general($request->id, $items);
        return $result;
    }

    public function inactivenumber(sms $sms, Request $request)
    {


        $items = [
            'status_id' => 2,
        ];
        $result = $sms->update_subdetails($request->id, $items);

        $count = $sms->getcounts($request->smsid, 1);

        if ($count[0]->counts == 0) {
            $items = [
                'status_id' => 2,
            ];
            $result = $sms->update_general($request->smsid, $items);
        }
        return $result;
    }



    public function inactiveall(sms $sms, Request $request)
    {

        $items = [
            'status_id' => 2,
        ];
        $result = $sms->update_subdetails_bysmsid($request->smsid, $items);

        $count = $sms->getcounts($request->smsid, 1);

        if ($count[0]->counts == 0) {
            $items = [
                'status_id' => 2,
            ];
            $result = $sms->update_general($request->smsid, $items);
        }
        return $result;
    }


    public function inactivedetails(sms $sms, Request $request)
    {

        $result = $sms->getsmsgeneral_inactive();
        return $result;
    }


    public function reactive(sms $sms, Request $request)
    {

        $items = [
            'status_id' => 1,
        ];
        $result = $sms->update_subdetails($request->id, $items);

        $count = $sms->getcounts($request->smsid, 2);

        if ($count[0]->counts == 0) {
            $items = [
                'status_id' => 1,
            ];
            $result = $sms->update_general($request->smsid, $items);
        }
        return $result;
    }
}

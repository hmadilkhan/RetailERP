<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use URL;

class custom_helper
{

    public static function getComissionSalesTotalAmount($commision, $from, $to, $provider_id)
    {
        $data = array();

        $select = "SELECT sum(credit) AS total_amount  FROM service_provider_ladger WHERE provider_id = " . $provider_id . " AND date_format(`date`,'%Y-%m-%d') >= '" . $from . "' AND date_format(`date`,'%Y-%m-%d') <= '" . $to . "' AND closed = 1 ";
        // echo $select;exit;
        $data = DB::select($select);
        if (count($data) > 0) {
            return $data[0]->total_amount;
        }
        return $data;
    }

    public static function getReceiptID($id)
    {

        $select = "SELECT receipt_no FROM sales_receipts WHERE id = $id";
        $data = DB::select($select);
        if (count($data) > 0) {
            return $data[0]->receipt_no;
        }
        return 0;
    }

    public static function getDueTotal($received)
    {
        $totalQuantity = 0;
        $totalTax = 0;
        $totalQuantityPrice = 0;
        foreach ($received as $val) {
            $totalQuantity = $val->quantity - $val->qty_return;
            $totalQuantityPrice += $totalQuantity * $val->total_amount;
        }
        return $totalQuantityPrice;
    }

    public static function getSubTotal($received)
    {
        $totalQuantity = 0;
        $totalTax = 0;
        $totalQuantityPrice = 0;
        // echo '<pre>';print_r($received);exit;
        foreach ($received as $val) {
            $totalQuantity = $val->quantity - $val->qty_return;
            $totalQuantityPrice += $totalQuantity * $val->price;
        }
        return $totalQuantityPrice;
    }

    public static function getTaxTotal($received)
    {
        $totalQuantity = 0;
        $totalTax = 0;
        foreach ($received as $val) {
            $totalQuantity = $val->quantity - $val->qty_return;
            // $perItemTax = $val->tax_per_item_value / $val->quantity;
            $totalTax +=  $totalQuantity * $val->tax_per_item_value;
        }
        return $totalTax;
    }

    public static function getLedgerCal($value, $receipt_balance, $total_amount, $credit, $debit, $creditGreater)
    {
        if ($value->total_amount == 0 && $value->credit == 0) {
            if ($value->receipt_no != '') {
                $receipt_balance = $receipt_balance + $value->debit;
            } else {
                if ($creditGreater == true) {
                    $receipt_balance = abs($receipt_balance - $value->debit);
                } else {
                    $receipt_balance = abs($receipt_balance + $value->debit);
                }
            }
        } elseif ($value->total_amount > 0) {
            if ($creditGreater == true && $value->receipt_no == 0) {
                $receipt_balance = abs($receipt_balance - $value->debit);
            } elseif ($creditGreater == true) {
                $receipt_balance = abs($receipt_balance - $value->debit);
            } else {
                $receipt_balance += $value->total_amount - $value->credit;
            }
        } elseif ($value->total_amount < 1) {
            if ($value->debit != 0) {
                $receipt_balance += abs($value->credit - $value->debit);
            } else {
                if ($receipt_balance != 0) {
                    if ($credit > $total_amount) {
                        if ($total_amount != 0) {
                            if ($creditGreater == true) {
                                $receipt_balance = $receipt_balance + $value->credit;
                            } else {
                                $receipt_balance = abs($receipt_balance - $value->credit);
                            }
                        } else {
                            $receipt_balance = $receipt_balance + $value->credit;
                        }
                    } else {
                        $receipt_balance = abs($receipt_balance - $value->credit);
                    }
                } else {
                    $receipt_balance =  $value->credit;
                }
            }
        }
        return abs($receipt_balance);
    }

    public function sendPushNotification($title, $message, $name)
    {

        $tokens = array();
        $result = DB::select("SELECT branch_name,b.name as company FROM `branch` INNER Join company b on b.company_id = branch.company_id where branch.company_id = ? and branch_id = ?", [session("company_id"), session("branch")]);

        $firebaseToken = DB::table("terminal_details")->where("branch_id", session("branch"))->whereNotNull("device_token")->get("device_token"); //DB::table("user_details")->where("company_id",session("company_id"))->whereNotNull("device_token")->get("device_token");//["cZIiT3EPTAKce8s8lPHTkZ:APA91bH0a0zModJDvMjwLmeMIqHNfyLriX1m2EWV9BI157KY6DtxsfWPDo-mYjl-Qh92dyfjU0Q0BM_HeXykZp6xy3LxoOxZmeLIxyBTimnfsCVIOuM0PBE8j53EV-_AWi6CVMOJIDMH"];//User::whereNotNull('device_token')->pluck('device_token')->all();
        foreach ($firebaseToken as $token) {
            array_push($tokens, $token->device_token);
        }
        $SERVER_API_KEY = 'AAAATXdhnIk:APA91bHFZZbCubOgnG3dihDVsqFbwGGQaBpC6f7BPFMnvpntpOOY88ysAEVAT2puQvdng3Xkd8j4HNVWFp1FQ2rHEe9g3Cv6nSZ7oeMsQtSh2GrJYNIxGHeogmen7TSPqRWHJxrG4QF_';

        $server_api_key_mobile = 'AAAA2dlOr6s:APA91bHGDpYDSZWI0LotnIYZUTpOTA9lLS56jsyB-2hq6Fsq6l0OPBoMYFqePTAbteVFawWzdyZOfMowMf-j8LBL8xJefdnpb_pZRVQHzu5rXykkdLBfPJgcr8gmPhPBDlXMWJy_-uv2';

        $data = [
            "registration_ids" => $tokens,
            "notification" => [
                "title" => "New Customer Added",
                "body" => $name,
                "icon" => "https://sabsoft.com.pk/Retail/public/assets/images/Sabify72.png",
                "content_available" => true,
                "priority" => "high",
            ],
            "data" => [
                "par1" => $message,
            ],
        ];
        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $response = curl_exec($ch);

        $headers1 = [
            'Authorization: key=' . $server_api_key_mobile,
            'Content-Type: application/json',
        ];

        $chs = curl_init();

        curl_setopt($chs, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($chs, CURLOPT_POST, true);
        curl_setopt($chs, CURLOPT_HTTPHEADER, $headers1);
        curl_setopt($chs, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($chs, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chs, CURLOPT_POSTFIELDS, $dataString);


        $response = curl_exec($ch);
        $responseOne = curl_exec($chs);

        return json_encode($response);
    }

    public static function getColorName($status)
    {
        if ($status == "Pending") {
            return "label-danger";
        } else if ($status == "Processing") {
            return "label-warning";
        } else if ($status == "Delivered") {
            return "label-success";
        } else if ($status == "Cancel") {
            return "label-danger";
        } else if ($status == "Ready for Delivery") {
            return "label-primary";
        } else if ($status == "Void") {
            return "label-danger";
        } else if ($status == "Sales Return") {
            return "label-danger";
        } else {
            return "label-success";
        }
    }

    public static function getOrderStatus($statusName, $isSaleReturn)
    {
        return $statusName;
        // if ($statusName == "Void" && $isSaleReturn == 1) {
        //     return "Sale Return";
        // } else {
        //     return $statusName;
        // }
    }

    public static function getProductImageUrl($inventory)
    {
        $imageUrl = asset('storage/images/no-image.png');

        if (in_array(session('company_id'), [95, 102, 104])) {
            if (!empty($inventory->product_image_url)) {
                $imageUrl = $inventory->product_image_url;
            } elseif (!empty($inventory->product_image)) {
                $imageUrl = asset('storage/images/products/' . $inventory->product_image);
            }
        } else {
            if (!empty($inventory->product_image)) {
                $imageUrl = asset('storage/images/products/' . $inventory->product_image);
            }
        }

        return $imageUrl;
    }

    public static function getProductImage($url, $image)
    {
        $imageUrl = asset('storage/images/no-image.png');

        if (in_array(session('company_id'), [95, 102, 104])) {
            if ($url != "" or $url != null) {
                $imageUrl = $url;
            } elseif (!empty($image)) {
                $imageUrl = asset('storage/images/products/' . $image);
            }
        } else {
            if (!empty($image)) {
                $imageUrl = asset('storage/images/products/' . $image);
            }
        }

        return $imageUrl;
    }
}

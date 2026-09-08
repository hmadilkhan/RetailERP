<?php

namespace App\Http\Controllers;

use App\Models\Order as OrderModel;
use Illuminate\Http\Request;
use Session;

class WebsiteOrderRedirectController extends Controller
{
    public function redirectToWebsiteOrder(Request $request)
    {
        $orderModel = OrderModel::with("website", "customer")->where("url_orderid", $request->id)->first();

        if ($orderModel == null) {
            Session::flash('error', 'Error! order detail not found.');
            return redirect('web-orders-view');
        }

        if ($orderModel->website == null || empty($orderModel->website->url)) {
            Session::flash('error', 'Error! website detail not found against this order.');
            return redirect('web-orders-view');
        }

        $url = rtrim($orderModel->website->url, '/') . '/order-status/' .  ($orderModel->customer->id ?? ''). '/' .$orderModel->url_orderid ;

        return redirect()->away($url);
    }
}

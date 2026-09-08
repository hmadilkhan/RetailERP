<?php

namespace App\Http\Controllers;

use App\Models\Order as OrderModel;
use Illuminate\Http\Request;

class WebsiteOrderRedirectController extends Controller
{
    public function redirectToWebsiteOrder(Request $request)
    {
        $orderModel = OrderModel::with("website", "customer")->where("url_orderid", $request->id)->first();

        if ($orderModel == null) {
            return view("404");
        }

        if ($orderModel->website == null || empty($orderModel->website->url)) {
            return view("404");
        }

        $url = rtrim($orderModel->website->url, '/') . '/order-status/' .  ($orderModel->customer->id ?? ''). '/' .$orderModel->url_orderid ;

        return redirect()->away($url);
    }
}

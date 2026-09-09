<?php

namespace App\Http\Controllers;

use App\Models\Order as OrderModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebsiteOrderRedirectController extends Controller
{
    public function redirectToWebsiteOrder(Request $request)
    {
        // Purane WhatsApp messages mein template ka placeholder link ka hissa ban gaya tha
        // (.../website-order-redirect/{{1}}WqSMKKLxg), is liye usay hata kar dhoondte hain.
        $orderId = trim(preg_replace('/\{\{\s*\d+\s*\}\}/', '', (string) $request->id));

        if ($orderId !== (string) $request->id) {
            Log::info("website-order-redirect: placeholder stripped from url", [
                "raw"     => $request->id,
                "cleaned" => $orderId,
            ]);
        }

        $orderModel = $orderId === ""
            ? null
            : OrderModel::with("website", "customer")->where("url_orderid", $orderId)->first();

        if ($orderModel == null) {
            Log::warning("website-order-redirect: order not found", ["url_orderid" => $request->id]);
            return view("404");
        }

        if ($orderModel->website == null || empty($orderModel->website->url)) {
            Log::warning("website-order-redirect: website url missing", ["url_orderid" => $request->id, "order_id" => $orderModel->id]);
            return view("404");
        }

        $url = rtrim($orderModel->website->url, '/') . '/order-status/' .  ($orderModel->customer->id ?? ''). '/' .$orderModel->url_orderid ;

        return redirect()->away($url);
    }
}

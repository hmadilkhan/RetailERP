<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{
	public function Test(){
		return 1;
	}
    public function login(Request $request)
    {
        $response = Http::asForm()->post('https://api.bsecure.pk/v1/oauth/token', [
            'client_id' => 'a1e99661-a3be-4e0f-820a-6de04cd61b1e',
            'client_secret' => '6jfPxlf83QoOKnuCk71kxwwbtBXQjjEI8bd0rtydnxw=',
            'grant_type' => 'client_credentials',
        ]);

        return $response;
    }

    public function create_order(Request $request)
    {


        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$request->token,
            'Accept'        => 'application/json',
        ])->asForm()->post('https://api.bsecure.pk/v1/order/create', [
            'order_id' => $request->orderid,
            'currency_code' => 'PKR',
            'total_amount' => 2500,
            'sub_total_amount' => 2500,
            'discount_amount' => 0,
            'products[0][id]' => '1',
            'products[0][name]' => 'Product 1',
            'products[0][quantity]' => 2,
            'products[0][sku]' => '1001',
            'products[0][sale_price]' => '2500',
            'products[0][price]' => '2500',
            'products[0][image]' => 'https://url.com/a.png',
            'products[0][short_description]' => 'client_credentials',
            'products[0][description]' => 'client_credentials',
            'products[0][discount]' => '0',
            'products[0][sub_total]' => '2500',
            'customer[name]' => 'Muhammad Adil Khan',
            'customer[email]' => 'adilsziu@outlook.com',
            'customer[country_code]' => '92',
            'customer[phone_number]' => '3112108156',
            'customer[auth_code]' => '',
        ]);

        return $response;
    }
}

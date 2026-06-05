<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Easypaisa\EasypaisaService;
use Illuminate\Http\Request;

class EasypaisaTestController extends Controller
{
    public function testOtc(Request $request, EasypaisaService $easypaisa)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'msisdn' => ['required', 'regex:/^03[0-9]{9}$/'],
            'email' => ['required', 'email'],
            'order_id' => ['nullable', 'string'],
        ]);

        return response()->json(
            $easypaisa->initiateOtc($validated)
        );
    }

    public function testMa(Request $request, EasypaisaService $easypaisa)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'mobile_account_no' => ['required', 'regex:/^03[0-9]{9}$/'],
            'email' => ['required', 'email'],
            'order_id' => ['nullable', 'string'],
        ]);

        return response()->json(
            $easypaisa->initiateMa($validated)
        );
    }

    public function inquire(Request $request, EasypaisaService $easypaisa)
    {
        $validated = $request->validate([
            'order_id' => ['required', 'string'],
        ]);

        return response()->json(
            $easypaisa->inquireTransaction($validated)
        );
    }
}
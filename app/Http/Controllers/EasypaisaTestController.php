<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Easypaisa\EasypaisaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EasypaisaTestController extends Controller
{
    public function testOtc(Request $request, EasypaisaService $easypaisa)
    {
        $validated = $this->validateJson($request, [
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
        $validated = $this->validateJson($request, [
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
        $validated = $this->validateJson($request, [
            'order_id' => ['required', 'string'],
        ]);

        return response()->json(
            $easypaisa->inquireTransaction($validated)
        );
    }

    public function maInfo()
    {
        return response()->json([
            'success' => false,
            'message' => 'Use POST /api/staging/easypaisa/ma to initiate an Easypaisa mobile account transaction.',
            'required_fields' => [
                'amount' => 'numeric, minimum 1',
                'mobile_account_no' => '03XXXXXXXXX',
                'email' => 'valid email address',
            ],
            'optional_fields' => [
                'order_id' => 'string',
            ],
        ]);
    }

    private function validateJson(Request $request, array $rules): array
    {
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            abort(response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422));
        }

        return $validator->validated();
    }
}

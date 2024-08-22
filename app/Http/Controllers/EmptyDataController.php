<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\emptyData;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use DateTime;

class EmptyDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function view(emptyData $emptydata){
        $company = $emptydata->getCompany();
        return view('EmptyData.view', compact('company'));
    }

    public function deletedatabase(emptyData $emptydata, Request $request)
    {
        $result = $emptydata->deletedatabase($request->copmanyid,$request->erp,$request->hr);
        return $result;
    }
}


//view-companies
<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class CustomerService
{
    public function getCustomers()
    {
        $result = DB::table('customers')->where("company_id", session("company_id"))->where('status_id', 1)->get();
        return $result;
    }
}

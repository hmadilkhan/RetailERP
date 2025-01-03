<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class CustomerService
{
    public function getCustomers()
    {
        $result = DB::table('customers')
                ->leftJoin("user_authorization","user_authorization.user_id","=","customers.user_id")
                ->leftJoin("branch","branch.branch_id","=","user_authorization.branch_id")
                ->where("customers.company_id", session("company_id"))
                ->where('customers.status_id', 1)
                ->select("customers.id","customers.name","branch.branch_id","branch.branch_name as branch_name")
                // ->limit(50)
                ->get();
        return $result;
    }
}

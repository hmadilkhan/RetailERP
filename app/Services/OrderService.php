<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\ServiceProvider;

class OrderService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getServiceProviders($branch="")
    {
        $serviceProvider = ServiceProvider::query();

        if (session("roleId") == 2) {
            $serviceProvider->whereIn("branch_id", Branch::where("company_id", session("company_id"))->pluck("branch_id"));
        } else {
            $serviceProvider->where("branch_id", session("branch"));
        }
        if (is_array($branch) && $branch != "" && $branch != "all") {
            $serviceProvider->whereIn("branch_id", $branch);
        }else{
            $serviceProvider->where("branch_id", $branch);
        }
        $serviceProvider->with("serviceprovideruser")->where("status_id", 1)->groupBy('id');
        return $serviceProvider->get();
    }
}

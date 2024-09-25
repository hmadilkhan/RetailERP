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
        if ($branch != "") {
            $serviceProvider->where("branch_id", $branch);
        }
        $serviceProvider->with("serviceprovideruser")->where("status_id", 1)->select("id","provider_name");
        return $serviceProvider->get();
    }
}

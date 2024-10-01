<?php

namespace App\Services;

use App\Models\Branch;

class BranchService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getBranches()  
    {
        $branches = [];
        if (session("roleId") == 2) {
            $branches = Branch::where("company_id",session("company_id"))->get();
        } else{
            $branches = Branch::where("branch_id",session("branch"))->get();
        }   

        return $branches;
    }
}

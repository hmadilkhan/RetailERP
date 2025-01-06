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
            $branches = Branch::where("company_id",session("company_id"))->where("status_id",1)->get();
        } else{
            $branches = Branch::where("branch_id",session("branch"))->where("status_id",1)->get();
        }   

        return $branches;
    }

    public function getBranchesPaginated()  
    {
        $branches = [];
        if (session("roleId") == 1) {
            $branches = Branch::with("terminals","city")->get();
        }else if (session("roleId") == 2) {
            $branches = Branch::with("terminals","city")->where("company_id",session("company_id"))->where("status_id",1)->get();
        } else{
            $branches = Branch::with("terminals","city")->where("branch_id",session("branch"))->where("status_id",1)->get();
        }   

        return $branches;
    }
}

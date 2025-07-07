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
            $branches = Branch::with("terminals","city")->where("status_id",1)->get();
        }else if (session("roleId") == 2) {
            $branches = Branch::with("terminals","city")->where("company_id",session("company_id"))->where("status_id",1)->get();
        } else{
            $branches = Branch::with("terminals","city")->where("branch_id",session("branch"))->where("status_id",1)->get();
        }   

        return $branches;
    }

    public function createHeadOffice($companyId, array $data)
    {
        $items = [
            'company_id' => $companyId,
            'country_id' => $data['country'],
            'city_id' => $data['city'],
            'status_id' => 1,
            'branch_name' => 'Head Office -' . $data['companyname'],
            'branch_address' => $data['company_address'],
            'branch_latitude' => null,
            'branch_longitude' => null,
            'branch_ptcl' => $data['company_ptcl'],
            'branch_mobile' => $data['company_mobile'],
            'branch_email' => $data['company_email'],
            'branch_logo' => $data['vdimg'] ?? '',
            'modify_by' => session('userid'),
            'modify_date' => date('Y-m-d'),
            'modify_time' => date('H:i:s'),
            'date' => date('Y-m-d'),
            'time' => date('H:i:s'),
        ];
        return Branch::insert($items);
    }
}

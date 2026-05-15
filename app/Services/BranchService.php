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
        if (session("roleId") == 2 || session("roleId") == 17) {
            $branches = Branch::where("company_id",session("company_id"))->where("status_id",1)->get();
        } else{
            $branches = Branch::where("branch_id",session("branch"))->where("status_id",1)->get();
        }   

        return $branches;
    }

    public function getBranchesPaginated(string $search = '', int $perPage = 15)
    {
        $query = Branch::with(['terminals', 'city'])->where('status_id', 1);

        if (session('roleId') == 1) {
            // all branches
        } elseif (session('roleId') == 2) {
            $query->where('company_id', session('company_id'));
        } else {
            $query->where('branch_id', session('branch'));
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('branch_name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('branch_mobile', 'like', "%{$search}%")
                  ->orWhere('branch_email', 'like', "%{$search}%")
                  ->orWhere('branch_address', 'like', "%{$search}%")
                  ->orWhereHas('city', fn($c) => $c->where('city_name', 'like', "%{$search}%"));
            });
        }

        return $query->orderBy('branch_id', 'desc')->paginate($perPage)->withQueryString();
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

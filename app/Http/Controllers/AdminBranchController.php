<?php

namespace App\Http\Controllers;

use App\adminBranch;
use App\branch;
use Illuminate\Http\Request;

class AdminBranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
      $this->middleware('auth:admin');
    }

    public function index(branch $branch)
    {
        $details = $branch->getBranchesforAdmin();
        return view('Admin.Branch.list', compact('details')); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(branch $branch)
    {
        $country = $branch->getcountry();
        $city = $branch->getcity();
        $company = $branch->getCompany();
        return view('Admin.Branch.create', compact('country','city','company'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(branch $branch,Request $request)
    {
 
        $imageName= "";

        $check = $branch->exist($request->branchname, session('company_id'));

        if ($check[0]->counter == 0) {
      
                if(!empty($request->vdimg)){
                     $request->validate([
                          'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                      ]);
                      $imageName = $request->branchname.'.'.$request->vdimg->getClientOriginalExtension();
                      $request->vdimg->move(public_path('assets/images/branch/'), $imageName);
                }

            $items = [
                'company_id' => $request->company,
                'country_id' => $request->country,
                'city_id' => $request->city,
                'status_id' => 1,
                'branch_name' => $request->branchname,
                'branch_address' => $request->br_address,
                'branch_latitude' => null,
                'branch_longitude' => null,
                'branch_ptcl' => $request->br_ptcl,
                'branch_mobile' => $request->br_mobile,
                'branch_email' => $request->br_email,
                'branch_logo' => $imageName,
                'modify_by' => session('userid'),
                'modify_date' => date('Y-m-d'),
                'modify_time' => date('H:i:s'),
                'date' => date('Y-m-d'),
                'time' => date('H:i:s'),
             ];
             
            
                $branch = $branch->insert_branch($items);
                 return 1;
            }    
            else {
             return 0;
            }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\adminBranch  $adminBranch
     * @return \Illuminate\Http\Response
     */
    public function show(adminBranch $adminBranch)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\adminBranch  $adminBranch
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,branch $branch)
    {
        $country = $branch->getcountry();
        $city = $branch->getcity();
        $company = $branch->getCompany();
        $details = $branch->getBranchById($request->id);
        return view('Admin.Branch.edit', compact('country','city','company','details'));
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\adminBranch  $adminBranch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, branch $branch)
    {
         $imageName= "";

        
         
                if(!empty($request->vdimg)){

                    $path = public_path('assets/images/branch/').$request->branchLogo;
                    if(file_exists($path)){
                        @unlink($path);
                    }
                    

                     $request->validate([
                          'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                      ]);
                      $imageName = $request->branchname.'.'.$request->vdimg->getClientOriginalExtension();
                      $request->vdimg->move(public_path('assets/images/branch/'), $imageName);

                    $items = [
                        'company_id' => $request->br_company,
                        'country_id' => $request->country,
                        'city_id' => $request->city,
                        'status_id' => 1,
                        'branch_name' => $request->branchname,
                        'branch_address' => $request->br_address,
                        'branch_latitude' => null,
                        'branch_longitude' => null,
                        'branch_ptcl' => $request->br_ptcl,
                        'branch_mobile' => $request->br_mobile,
                        'branch_email' => $request->br_email,
                        'branch_logo' => $imageName, 
                        'modify_by' => session('userid'),
                        'modify_date' => date('Y-m-d'),
                        'modify_time' => date('H:i:s'),
                    ];
                    $branch = $branch->branch_update($request->branchId, $items);
                     return 1;

               }else{

                    $items = [
                        'company_id' => $request->br_company,
                        'country_id' => $request->country,
                        'city_id' => $request->city,
                        'status_id' => 1,
                        'branch_name' => $request->branchname,
                        'branch_address' => $request->br_address,
                        'branch_latitude' => null,
                        'branch_longitude' => null,
                        'branch_ptcl' => $request->br_ptcl,
                        'branch_mobile' => $request->br_mobile,
                        'branch_email' => $request->br_email,
                        'modify_by' => session('userid'),
                        'modify_date' => date('Y-m-d'),
                        'modify_time' => date('H:i:s'),
                     ];
                   
                     $branch = $branch->branch_update($request->branchId, $items);
                     return 1;
                 
               }//else bracket

           

              
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\adminBranch  $adminBranch
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,branch $branch)
    {
        $result = $branch->branch_remove($request->id);
        return 1;
    }
}

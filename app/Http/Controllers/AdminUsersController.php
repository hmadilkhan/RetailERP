<?php

namespace App\Http\Controllers;

use App\userDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AdminUsersController extends Controller
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
    
    public function index(userDetails $users)
    {
        $getusers =$users->get_users();
        return view('Admin.Users.list', compact('getusers')); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, userDetails $users)
    {
        $country = $users->getcountry();
        $city = $users->getcity();
        $role = $users->getrolesForAdmin();
        $company = $users->getCompany();
        return view('Admin.Users.create', compact('country','city','role','company')); 
    }

    public function chk_user_exists(Request $request, userDetails $users){
        $count = $users->chk_user($request->username);
        return $count;
    }  

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(userDetails $users,Request $request)
    {

        $imageName= "";
         $rules = [
                'fullname' => 'required',
                'email' => 'required|email',
                'contact' => 'required',
                'username' => 'required',
                'password' => 'required',
            ];
             $this->validate($request, $rules);

            if(!empty($request->vdimg)){
                     $request->validate([
                          'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                      ]);
                      $imageName = $request->username.'.'.$request->vdimg->getClientOriginalExtension();
                      $request->vdimg->move(public_path('assets/images/users/'), $imageName);
            }

            $exist = $users->exist($request->username);
            if ($exist == 0) {
                    $items=[
                        'fullname' => $request->fullname,
                        'username' => $request->username,
                        'password' => Hash::make($request->password),
                        'email' => $request->email,
                        'contact' => $request->contact,
                        'country_id' => $request->country,
                        'city_id' => $request->city,
                        'address' => $request->address,
                        'image' => $imageName,
                        'remember_token' => null,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'show_password' => $request->password,
                    ];
                    $user = $users->insert_user('user_details',$items);
                  $items = [
                        'user_id' => $user,
                        'company_id' => $request->company,
                        'branch_id' => $request->branch,
                        'role_id' => $request->role,
                        'status_id' => 1,
                    ];
                    $result = $users->insert_user('user_authorization',$items);
                   return redirect('view-users');

           }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\adminUsers  $adminUsers
     * @return \Illuminate\Http\Response
     */
    public function show(adminUsers $adminUsers)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\adminUsers  $adminUsers
     * @return \Illuminate\Http\Response
     */
    public function edit(userDetails $users,Request $request)
    {
        $country = $users->getcountry();
        $city = $users->getcity();
        $role = $users->getrolesForAdmin();
        $company = $users->getCompany();
        $branch = $users->getbranchesForAdmin($company[0]->company_id);
        $user =  $users->user_details($request->id);;
        return view('Admin.Users.edit', compact('country','city','role','company','user','branch')); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\adminUsers  $adminUsers
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, userDetails $users)
    {
        $imageName= "";
            $rules = [
                'fullname' => 'required',
                'email' => 'required|email',
                'contact' => 'required',
                'username' => 'required',
                'password' => 'required',
            ];
             $this->validate($request, $rules);

             
                if(!empty($request->vdimg)){

                    $image_path = public_path('assets/images/users/'.$request->prevImg);  // Value is not URL but directory file path
                    if($request->prevImg != "") {
                        if(file_exists($image_path)){
                            unlink($image_path);
                        }
                    }

                     $request->validate([
                          'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                      ]);
                      $imageName = $request->username.'.'.$request->vdimg->getClientOriginalExtension();
                      $request->vdimg->move(public_path('assets/images/users/'), $imageName);

                    $items=[
                        'fullname' => $request->fullname,
                        'username' => $request->username,
                        'password' => Hash::make($request->password),
                        'email' => $request->email,
                        'contact' => $request->contact,
                        'country_id' => $request->country,
                        'city_id' => $request->city,
                        'address' => $request->address,
                        'image' => $imageName,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'show_password' => $request->password,
                    ];
                    $user = $users->update_userdetails($request->userid,$items);

                }else{

                     $items=[
                        'fullname' => $request->fullname,
                        'username' => $request->username,
                        'password' => Hash::make($request->password),
                        'email' => $request->email,
                        'contact' => $request->contact,
                        'country_id' => $request->country,
                        'city_id' => $request->city,
                        'address' => $request->address,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'show_password' => $request->password,
                    ];
                    $user = $users->update_userdetails($request->userid,$items);

                }
                   

                    $items = [
                        'user_id' => $request->userid,
                        'company_id' => $request->company,
                        'branch_id' => $request->branch,
                        'role_id' => $request->role,
                        'status_id' => 1,
                    ];
                       $result = $users->update_user_authorization($request->authId,$items);
                       return redirect('view-users');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\adminUsers  $adminUsers
     * @return \Illuminate\Http\Response
     */
    public function destroy(userDetails $users,Request $request)
    {
        $result = $users->delete_user($request->id);
        return 1;
    }


    public function getBranches(Request $request, userDetails $users)
    {
        $result = $users->getBranchByCompany($request->id);
        return $result;
    }
}

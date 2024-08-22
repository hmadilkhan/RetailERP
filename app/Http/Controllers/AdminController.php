<?php

namespace App\Http\Controllers;

 
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Session;


class AdminController extends Controller
{


    // public function __construct()
    // {
    //   $this->middleware('auth');
    // }

    public function showLoginForm()
    {

        return view('admin-login');
    }

    public function loginsubmit(Request $request)
    {
    	$result = DB::table('user_details')->where(["username" => $request->username,"show_password" =>$request->password]);
    	if ($result != "") 
    	{
    		return redirect('/admin/dashboard');
    	}

        
    }

    public function index()
    {

        return view('Admin.dashboard');
    }

    public function logout()
    {
    	$request->session()->invalidate();
        return redirect('/');
    }


 


}



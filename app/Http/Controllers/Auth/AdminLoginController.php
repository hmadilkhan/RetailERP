<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    /**
     * Show the application’s login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('Admin.auth.login');
    }

    protected function guard(){
        return Auth::guard('admin');
    }
    
    use AuthenticatesUsers;
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/dashboard'; // actual it is redirected to /admin/dashboard
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }


    protected function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->flush(); // this method should be called after we ensure that there is no logged in guards left

        $request->session()->regenerate(); //same 

        return redirect()->route('admin.login');
    }
}
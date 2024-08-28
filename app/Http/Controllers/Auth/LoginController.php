<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // public function login(Request $request)
    // {
       
    //     $this->validate($request, [
    //         'username' => 'required',
    //         'password' => 'required',
    //     ]);
    //     Auth::logoutOtherDevices($request->password);
    //     $user = User::where('username', $request->input('username'))->first();
    //     // if ($user->isLoggedIn == 1) {
             
    //     //     // \Session::flash('message', 'Your are already login from other device. Please logout first');
    //     //     return redirect()->to('/login')->with('message', 'Your are already login from other device. Please logout first');
    //     // }
    //     if (auth()->guard('web')->attempt(['username' => $request->input('username'), 'password' => $request->input('password')])) {

            
    //         $get_authorize = DB::table('user_authorization')->where(['user_id'=>$user->id,'status_id'=>1])->get();
            
    //         if($get_authorize->count() > 0){
    //             $get_authen_id =  DB::table("user_authentication")->insertGetId(['user_id'=>$user->id, 'authorization_id'=>$get_authorize[0]->authorization_id,'password'=>$user->password,'username'=>$user->username,'login_datetime'=>date("Y-m-d H:i:s"),'logout_datetime'=>date("Y-m-d H:i:s")]);
               
    //             $get_role_id = DB::table("user_authorization")->where('user_id',$user->id)->get();
    //             $get_image = DB::table("user_details")->where('id',$user->id)->get();
    
    //             session(['userid'=>$user->id,'company_id'=>$get_authorize[0]->company_id,"branch"=>$get_authorize[0]->branch_id,'authenticated_id'=>$get_authen_id,'login_msg'=> 'Welcome to '.$user->username,'roleId'=>$get_role_id[0]->role_id,'image' => $get_image[0]->image]);
    //         }
    //         // User::where('id', $user->id)->update(['isLoggedIn' => 1]);
    //         return redirect()->intended($this->redirectPath());
    //     }
    //     \Session::put('login_error', 'Your email and password wrong!!');
    //     return back();

    // }

    // public function logout(Request $request)
    // {
    //     $user = User::where('username', $request->input('username'))->first();
    //     User::where('id', auth()->user()->id)->update(['isLoggedIn' => 0]);
    //     \Session::flush();
    //     \Session::put('success', 'Logout Successful!');
    //     return redirect()->to('/login');
    // }
}

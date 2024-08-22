<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            Auth::logout();
            return redirect('/login');
        }

        $result = DB::table("user_authorization")->where("user_id", Auth::user()->id)->first();

        if ($result->status_id != 1) {
            Auth::logout();
            return redirect('/login');
        }

        return $next($request);
        // $result = DB::table("user_authorization")->where("user_id",Auth::user()->id)->first();
        //       if ($result->status_id == 1) {
        //           return $next($request);
        //       }else{
        // 	Auth::logout() ;
        // 	return redirect('/login');
        // }
    }
}

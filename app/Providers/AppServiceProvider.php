<?php

namespace App\Providers;

use App\Services\QuickBooks\QuickBooksAuthService;
use App\Services\QuickBooks\QuickBooksCustomerService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use MongoDB\Driver\Session;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //        if (session("roleId") != null)
        //        {
        //        $pageid = DB::select('SELECT page_id from role_settings WHERE role_id = ? ORDER BY page_id',[session("roleId")]);
        ////        View::share('pageid',DB::select('SELECT page_id from role_settings WHERE role_id = ? ORDER BY page_id',[session("roleId")]));
        //        View::share('result',DB::table('pages_details')->where('id',$pageid)->get());
        //        }
        //        else{
        //            View::share('result',DB::table('pages_details')->get());
        //        }
        //        View::share('result',DB::table('pages_details')->get());
    }
}

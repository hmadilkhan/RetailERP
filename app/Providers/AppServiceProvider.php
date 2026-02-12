<?php

namespace App\Providers;

use App\Services\QuickBooks\QuickBooksAuthService;
use App\Services\QuickBooks\QuickBooksCustomerService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use MongoDB\Driver\Session;
use Spatie\Activitylog\ActivityLogger;

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
        Paginator::useBootstrap();
        
        ActivityLogger::macro('withCompany', function ($companyId) {
            $this->tap(function ($activity) use ($companyId) {
                $activity->company_id = $companyId;
            });

            return $this;
        });

        ActivityLogger::macro('withBranch', function ($branchId) {
            $this->tap(function ($activity) use ($branchId) {
                $activity->branch_id = $branchId;
            });

            return $this;
        });
    }
}

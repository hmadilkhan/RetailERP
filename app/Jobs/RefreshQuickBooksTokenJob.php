<?php

namespace App\Jobs;

use App\Services\QuickBooks\QuickBooksAuthService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshQuickBooksTokenJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(QuickBooksAuthService $authService)
    {
        $authService->refreshAccessToken();
    }
}

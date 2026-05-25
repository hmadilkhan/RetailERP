<?php

use App\Http\Controllers\V2\AdminDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'statusCheck'])
    ->prefix('v2')
    ->as('v2.')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('index');
        Route::get('/admin-dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    });

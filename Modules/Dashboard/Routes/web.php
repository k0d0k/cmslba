<?php

use Illuminate\Support\Facades\Route;
use Modules\Dashboard\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Routes modul Dashboard
|--------------------------------------------------------------------------
| Route didaftarkan sendiri oleh modul sehingga bersifat mandiri. Nama route
| tetap mengikuti konvensi inti: admin.dashboard (root grup admin).
*/

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    });

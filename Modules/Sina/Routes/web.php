<?php

use Illuminate\Support\Facades\Route;
use Modules\Sina\Http\Controllers\SinaController;

/*
|--------------------------------------------------------------------------
| Routes modul Sina
|--------------------------------------------------------------------------
| Route didaftarkan sendiri oleh modul sehingga bersifat mandiri. Nama route
| tetap mengikuti konvensi inti: admin.sina.*
*/

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        $controller = SinaController::class;

        Route::get('sina', [$controller, 'index'])->name('sina.index');
        Route::get('sina/data', [$controller, 'data'])->name('sina.data');
        Route::get('sina/topups', [$controller, 'topups'])->name('sina.topups');
        Route::post('sina/config', [$controller, 'updateConfig'])->name('sina.config');
        Route::post('sina/token-price', [$controller, 'savePricing'])->name('sina.pricing');
        Route::post('sina/topup', [$controller, 'topUp'])->name('sina.topup');
    });

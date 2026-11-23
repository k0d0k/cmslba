<?php

use Illuminate\Support\Facades\Route;
use Modules\Mitra\Http\Controllers\MitraController;

/*
|--------------------------------------------------------------------------
| Routes modul Mitra
|--------------------------------------------------------------------------
| Route didaftarkan sendiri oleh modul (bukan generator menu di
| AppServiceProvider) sehingga modul bersifat mandiri. Nama route tetap
| mengikuti konvensi inti: admin.mitras.*
*/

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        $controller = MitraController::class;

        Route::get('mitras', [$controller, 'index'])->name('mitras.index');
        Route::get('mitras/data', [$controller, 'data'])->name('mitras.data');
        Route::get('mitras/create', [$controller, 'create'])->name('mitras.create');
        Route::post('mitras', [$controller, 'store'])->name('mitras.store');
        Route::get('mitras/{mitra}/edit', [$controller, 'edit'])->name('mitras.edit');
        Route::put('mitras/{mitra}', [$controller, 'update'])->name('mitras.update');
        Route::delete('mitras/{mitra}', [$controller, 'destroy'])->name('mitras.destroy');

        Route::post('mitras/{mitra}/subscriptions', [$controller, 'subscribe'])->name('mitras.subscriptions.store');
        Route::post('mitras/{mitra}/subscriptions/{subscription}/cancel', [$controller, 'cancelSubscription'])->name('mitras.subscriptions.cancel');
        Route::post('mitras/{mitra}/subscriptions/{subscription}/activate', [$controller, 'activateSubscription'])->name('mitras.subscriptions.activate');

        Route::post('mitras/{mitra}/brands', [$controller, 'attachBrand'])->name('mitras.brands.store');
        Route::post('mitras/{mitra}/brands/{brand}/primary', [$controller, 'makePrimaryBrand'])->name('mitras.brands.primary');
        Route::delete('mitras/{mitra}/brands/{brand}', [$controller, 'detachBrand'])->name('mitras.brands.destroy');
    });

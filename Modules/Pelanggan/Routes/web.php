<?php

use Illuminate\Support\Facades\Route;
use Modules\Pelanggan\Http\Controllers\PelangganController;

/*
|--------------------------------------------------------------------------
| Routes modul Pelanggan
|--------------------------------------------------------------------------
| Route didaftarkan sendiri oleh modul (bukan generator menu di
| AppServiceProvider) sehingga modul bersifat mandiri. Nama route tetap
| mengikuti konvensi inti: admin.pelanggan.*
*/

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        $controller = PelangganController::class;

        Route::get('pelanggan', [$controller, 'index'])->name('pelanggan.index');
        Route::get('pelanggan/data', [$controller, 'data'])->name('pelanggan.data');
        Route::get('pelanggan/create', [$controller, 'create'])->name('pelanggan.create');
        Route::post('pelanggan', [$controller, 'store'])->name('pelanggan.store');
        Route::get('pelanggan/{slug}/edit', [$controller, 'edit'])->name('pelanggan.edit');
        Route::put('pelanggan/{slug}', [$controller, 'update'])->name('pelanggan.update');
        Route::delete('pelanggan/{slug}', [$controller, 'destroy'])->name('pelanggan.destroy');
        Route::post('pelanggan/{slug}/reset-password', [$controller, 'resetPassword'])->name('pelanggan.reset-password');
        Route::post('pelanggan/{slug}/archive', [$controller, 'archive'])->name('pelanggan.archive');
    });

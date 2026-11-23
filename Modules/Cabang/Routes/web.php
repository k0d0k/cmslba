<?php

use Illuminate\Support\Facades\Route;
use Modules\Cabang\Http\Controllers\CabangController;

/*
|--------------------------------------------------------------------------
| Routes modul Cabang
|--------------------------------------------------------------------------
| Route didaftarkan sendiri oleh modul (bukan generator menu di
| AppServiceProvider) sehingga modul bersifat mandiri. Nama route tetap
| mengikuti konvensi inti: admin.cabang.*
*/

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        $controller = CabangController::class;

        Route::get('cabang', [$controller, 'index'])->name('cabang.index');
        Route::get('cabang/data', [$controller, 'data'])->name('cabang.data');
        Route::get('cabang/create', [$controller, 'create'])->name('cabang.create');
        Route::post('cabang', [$controller, 'store'])->name('cabang.store');
        Route::get('cabang/{slug}/edit', [$controller, 'edit'])->name('cabang.edit');
        Route::put('cabang/{slug}', [$controller, 'update'])->name('cabang.update');
        Route::delete('cabang/{slug}', [$controller, 'destroy'])->name('cabang.destroy');
    });

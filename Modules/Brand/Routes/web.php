<?php

use Illuminate\Support\Facades\Route;
use Modules\Brand\Http\Controllers\BrandController;

/*
|--------------------------------------------------------------------------
| Routes modul Brand
|--------------------------------------------------------------------------
| Route didaftarkan sendiri oleh modul sehingga modul bersifat mandiri.
| Nama route tetap mengikuti konvensi inti: admin.brand.*
*/

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        $controller = BrandController::class;

        Route::get('brand', [$controller, 'index'])->name('brand.index');
        Route::get('brand/data', [$controller, 'data'])->name('brand.data');
        Route::get('brand/create', [$controller, 'create'])->name('brand.create');
        Route::post('brand', [$controller, 'store'])->name('brand.store');
        Route::get('brand/{slug}/edit', [$controller, 'edit'])->name('brand.edit');
        Route::put('brand/{slug}', [$controller, 'update'])->name('brand.update');
        Route::delete('brand/{slug}', [$controller, 'destroy'])->name('brand.destroy');
    });

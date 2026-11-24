<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes modul Practitioners
|--------------------------------------------------------------------------
| Route didaftarkan sendiri oleh modul (bukan generator menu di
| AppServiceProvider) sehingga modul bersifat mandiri. Nama route tetap
| mengikuti konvensi inti: admin.practitioners.*
*/

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        $controller = \Modules\Practitioners\Http\Controllers\PractitionerController::class;

        Route::get('practitioners', [$controller, 'index'])->name('practitioners.index');
        Route::get('practitioners/data', [$controller, 'data'])->name('practitioners.data');
        Route::get('practitioners/create', [$controller, 'create'])->name('practitioners.create');
        Route::post('practitioners', [$controller, 'store'])->name('practitioners.store');
        Route::get('practitioners/{slug}/edit', [$controller, 'edit'])->name('practitioners.edit');
        Route::put('practitioners/{slug}', [$controller, 'update'])->name('practitioners.update');
        Route::delete('practitioners/{slug}', [$controller, 'destroy'])->name('practitioners.destroy');
    });

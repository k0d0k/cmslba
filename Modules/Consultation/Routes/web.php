<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes modul Consultation
|--------------------------------------------------------------------------
| Route didaftarkan sendiri oleh modul (bukan generator menu di
| AppServiceProvider) sehingga modul bersifat mandiri. Nama route tetap
| mengikuti konvensi inti: admin.consultations.*
*/

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        $controller = \Modules\Consultation\Http\Controllers\ConsultationController::class;

        Route::get('consultations', [$controller, 'index'])->name('consultations.index');
        Route::get('consultations/data', [$controller, 'data'])->name('consultations.data');
        Route::get('consultations/create', [$controller, 'create'])->name('consultations.create');
        Route::post('consultations', [$controller, 'store'])->name('consultations.store');
        Route::get('consultations/{slug}/edit', [$controller, 'edit'])->name('consultations.edit');
        Route::put('consultations/{slug}', [$controller, 'update'])->name('consultations.update');
        Route::delete('consultations/{slug}', [$controller, 'destroy'])->name('consultations.destroy');
    });

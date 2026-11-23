<?php

use Illuminate\Support\Facades\Route;
use Modules\Appointments\Http\Controllers\AppointmentController;

/*
|--------------------------------------------------------------------------
| Routes modul Appointments
|--------------------------------------------------------------------------
| Route didaftarkan sendiri oleh modul (bukan generator menu di
| AppServiceProvider) sehingga modul bersifat mandiri. Nama route tetap
| mengikuti konvensi inti: admin.appointments.*
*/

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        $controller = AppointmentController::class;

        Route::get('appointments', [$controller, 'index'])->name('appointments.index');
        Route::get('appointments/data', [$controller, 'data'])->name('appointments.data');
        Route::get('appointments/create', [$controller, 'create'])->name('appointments.create');
        Route::post('appointments', [$controller, 'store'])->name('appointments.store');
        Route::get('appointments/{slug}/edit', [$controller, 'edit'])->name('appointments.edit');
        Route::put('appointments/{slug}', [$controller, 'update'])->name('appointments.update');
        Route::delete('appointments/{slug}', [$controller, 'destroy'])->name('appointments.destroy');
    });

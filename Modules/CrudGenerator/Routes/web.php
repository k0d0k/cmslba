<?php

use Illuminate\Support\Facades\Route;
use Modules\CrudGenerator\Http\Controllers\CrudGeneratorController;

/*
|--------------------------------------------------------------------------
| Routes modul CrudGenerator
|--------------------------------------------------------------------------
| Route didaftarkan sendiri oleh modul sehingga bersifat mandiri. Akses
| dibatasi untuk principal. Nama route tetap mengikuti konvensi inti:
| admin.generator.*
*/

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::middleware('role:admin_principal')->group(function () {
            Route::get('generator', [CrudGeneratorController::class, 'index'])->name('generator.index');
            Route::get('generator/create', [CrudGeneratorController::class, 'create'])->name('generator.create');
            Route::post('generator', [CrudGeneratorController::class, 'store'])->name('generator.store');
        });
    });

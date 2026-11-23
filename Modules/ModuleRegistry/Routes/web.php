<?php

use Illuminate\Support\Facades\Route;
use Modules\ModuleRegistry\Http\Controllers\ModuleController;

/*
|--------------------------------------------------------------------------
| Routes modul ModuleRegistry
|--------------------------------------------------------------------------
| Route didaftarkan sendiri oleh modul sehingga modul bersifat mandiri.
| Nama route tetap mengikuti konvensi inti: admin.modules.*
*/

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        $controller = ModuleController::class;

        Route::get('modules', [$controller, 'index'])->name('modules.index');
        Route::get('modules/data', [$controller, 'data'])->name('modules.data');
        Route::get('modules/create', [$controller, 'create'])->name('modules.create');
        Route::post('modules', [$controller, 'store'])->name('modules.store');
        Route::get('modules/{slug}/edit', [$controller, 'edit'])->name('modules.edit');
        Route::put('modules/{slug}', [$controller, 'update'])->name('modules.update');
        Route::delete('modules/{slug}', [$controller, 'destroy'])->name('modules.destroy');
    });

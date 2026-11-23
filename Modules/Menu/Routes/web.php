<?php

use Illuminate\Support\Facades\Route;
use Modules\Menu\Http\Controllers\MenuController;

/*
|--------------------------------------------------------------------------
| Routes modul Menu
|--------------------------------------------------------------------------
| Manajemen menu dinamis. Terbatas untuk admin_principal. Nama route tetap
| mengikuti konvensi inti: admin.menus.*
*/

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::middleware('role:admin_principal')->group(function () {
            $controller = MenuController::class;

            Route::get('menus', [$controller, 'index'])->name('menus.index');
            Route::post('menus', [$controller, 'store'])->name('menus.store');
            Route::put('menus/{menu}', [$controller, 'update'])->name('menus.update');
            Route::delete('menus/{menu}', [$controller, 'destroy'])->name('menus.destroy');
            Route::post('menus/reorder', [$controller, 'reorder'])->name('menus.reorder');
        });
    });

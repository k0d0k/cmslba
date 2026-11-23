<?php

use Illuminate\Support\Facades\Route;
use Modules\Role\Http\Controllers\RoleController;

/*
|--------------------------------------------------------------------------
| Routes modul Role
|--------------------------------------------------------------------------
| Role & akses. Terbatas untuk admin_principal. Nama route tetap mengikuti
| konvensi inti: admin.roles.*
*/

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::middleware('role:admin_principal')->group(function () {
            $controller = RoleController::class;

            Route::get('roles', [$controller, 'index'])->name('roles.index');
            Route::post('roles', [$controller, 'store'])->name('roles.store');
            Route::get('roles/{role}/assign', [$controller, 'assign'])->name('roles.assign');
            Route::post('roles/{role}/assign', [$controller, 'assign.save'])->name('roles.assign.save');
        });
    });

<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Routes modul User
|--------------------------------------------------------------------------
| Route didaftarkan sendiri oleh modul (bukan generator menu di
| AppServiceProvider) sehingga modul bersifat mandiri. Nama route tetap
| mengikuti konvensi inti: admin.users.*
*/

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        $controller = UserController::class;

        Route::get('users', [$controller, 'index'])->name('users.index');
        Route::get('users/data', [$controller, 'data'])->name('users.data');
        Route::get('users/create', [$controller, 'create'])->name('users.create');
        Route::post('users', [$controller, 'store'])->name('users.store');
        Route::get('users/{slug}/edit', [$controller, 'edit'])->name('users.edit');
        Route::put('users/{slug}', [$controller, 'update'])->name('users.update');
        Route::delete('users/{slug}', [$controller, 'destroy'])->name('users.destroy');
    });

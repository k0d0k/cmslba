<?php

use Illuminate\Support\Facades\Route;
use Modules\Events\Http\Controllers\EventController;

/*
|--------------------------------------------------------------------------
| Routes modul Events
|--------------------------------------------------------------------------
| Route didaftarkan sendiri oleh modul sehingga modul bersifat mandiri.
| Nama route tetap mengikuti konvensi inti: admin.events.*
*/

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        $controller = EventController::class;

        Route::get('events', [$controller, 'index'])->name('events.index');
        Route::get('events/data', [$controller, 'data'])->name('events.data');
        Route::get('events/create', [$controller, 'create'])->name('events.create');
        Route::post('events', [$controller, 'store'])->name('events.store');
        Route::get('events/{slug}/edit', [$controller, 'edit'])->name('events.edit');
        Route::put('events/{slug}', [$controller, 'update'])->name('events.update');
        Route::delete('events/{slug}', [$controller, 'destroy'])->name('events.destroy');
    });

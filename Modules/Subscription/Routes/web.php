<?php

use Illuminate\Support\Facades\Route;
use Modules\Subscription\Http\Controllers\SubscriptionController;

/*
|--------------------------------------------------------------------------
| Routes modul Subscription
|--------------------------------------------------------------------------
| Route didaftarkan sendiri oleh modul sehingga modul bersifat mandiri.
| Nama route tetap mengikuti konvensi inti: admin.subscriptions.*
*/

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        $controller = SubscriptionController::class;

        Route::get('subscriptions', [$controller, 'index'])->name('subscriptions.index');
        Route::get('subscriptions/data', [$controller, 'data'])->name('subscriptions.data');
        Route::get('subscriptions/create', [$controller, 'create'])->name('subscriptions.create');
        Route::post('subscriptions', [$controller, 'store'])->name('subscriptions.store');
        Route::get('subscriptions/{slug}/edit', [$controller, 'edit'])->name('subscriptions.edit');
        Route::put('subscriptions/{slug}', [$controller, 'update'])->name('subscriptions.update');
        Route::delete('subscriptions/{slug}', [$controller, 'destroy'])->name('subscriptions.destroy');
    });

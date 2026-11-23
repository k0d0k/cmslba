<?php

use Illuminate\Support\Facades\Route;
use Modules\PaymentProvider\Http\Controllers\PaymentProviderController;

/*
|--------------------------------------------------------------------------
| Routes modul PaymentProvider
|--------------------------------------------------------------------------
| Route didaftarkan sendiri oleh modul sehingga modul bersifat mandiri.
| Nama route tetap mengikuti konvensi inti: admin.payment_providers.*
*/

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        $controller = PaymentProviderController::class;

        Route::get('payment_providers', [$controller, 'index'])->name('payment_providers.index');
        Route::get('payment_providers/data', [$controller, 'data'])->name('payment_providers.data');
        Route::get('payment_providers/create', [$controller, 'create'])->name('payment_providers.create');
        Route::post('payment_providers', [$controller, 'store'])->name('payment_providers.store');
        Route::get('payment_providers/{slug}/edit', [$controller, 'edit'])->name('payment_providers.edit');
        Route::put('payment_providers/{slug}', [$controller, 'update'])->name('payment_providers.update');
        Route::delete('payment_providers/{slug}', [$controller, 'destroy'])->name('payment_providers.destroy');
    });

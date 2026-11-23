<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes modul Commerce
|--------------------------------------------------------------------------
| Route didaftarkan sendiri oleh modul (bukan generator menu di
| AppServiceProvider) sehingga modul bersifat mandiri. Nama route tetap
| mengikuti konvensi inti: admin.<key>.*
*/

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        $controller = \Modules\Commerce\Http\Controllers\ProductController::class;
        Route::get('products', [$controller, 'index'])->name('products.index');
        Route::get('products/data', [$controller, 'data'])->name('products.data');
        Route::get('products/create', [$controller, 'create'])->name('products.create');
        Route::post('products', [$controller, 'store'])->name('products.store');
        Route::get('products/{slug}/edit', [$controller, 'edit'])->name('products.edit');
        Route::put('products/{slug}', [$controller, 'update'])->name('products.update');
        Route::delete('products/{slug}', [$controller, 'destroy'])->name('products.destroy');

        $controller = \Modules\Commerce\Http\Controllers\ProductCategoryController::class;
        Route::get('product_categories', [$controller, 'index'])->name('product_categories.index');
        Route::get('product_categories/data', [$controller, 'data'])->name('product_categories.data');
        Route::get('product_categories/create', [$controller, 'create'])->name('product_categories.create');
        Route::post('product_categories', [$controller, 'store'])->name('product_categories.store');
        Route::get('product_categories/{slug}/edit', [$controller, 'edit'])->name('product_categories.edit');
        Route::put('product_categories/{slug}', [$controller, 'update'])->name('product_categories.update');
        Route::delete('product_categories/{slug}', [$controller, 'destroy'])->name('product_categories.destroy');

        $controller = \Modules\Commerce\Http\Controllers\OrderController::class;
        Route::get('orders', [$controller, 'index'])->name('orders.index');
        Route::get('orders/data', [$controller, 'data'])->name('orders.data');
        Route::get('orders/create', [$controller, 'create'])->name('orders.create');
        Route::post('orders', [$controller, 'store'])->name('orders.store');
        Route::get('orders/{slug}/edit', [$controller, 'edit'])->name('orders.edit');
        Route::put('orders/{slug}', [$controller, 'update'])->name('orders.update');
        Route::delete('orders/{slug}', [$controller, 'destroy'])->name('orders.destroy');

        $controller = \Modules\Commerce\Http\Controllers\InvoiceController::class;
        Route::get('invoices', [$controller, 'index'])->name('invoices.index');
        Route::get('invoices/data', [$controller, 'data'])->name('invoices.data');
        Route::get('invoices/create', [$controller, 'create'])->name('invoices.create');
        Route::post('invoices', [$controller, 'store'])->name('invoices.store');
        Route::get('invoices/{slug}/edit', [$controller, 'edit'])->name('invoices.edit');
        Route::put('invoices/{slug}', [$controller, 'update'])->name('invoices.update');
        Route::delete('invoices/{slug}', [$controller, 'destroy'])->name('invoices.destroy');

        $controller = \Modules\Commerce\Http\Controllers\PaymentTransactionController::class;
        Route::get('payment_transactions', [$controller, 'index'])->name('payment_transactions.index');
        Route::get('payment_transactions/data', [$controller, 'data'])->name('payment_transactions.data');
        Route::get('payment_transactions/create', [$controller, 'create'])->name('payment_transactions.create');
        Route::post('payment_transactions', [$controller, 'store'])->name('payment_transactions.store');
        Route::get('payment_transactions/{slug}/edit', [$controller, 'edit'])->name('payment_transactions.edit');
        Route::put('payment_transactions/{slug}', [$controller, 'update'])->name('payment_transactions.update');
        Route::delete('payment_transactions/{slug}', [$controller, 'destroy'])->name('payment_transactions.destroy');

        $controller = \Modules\Commerce\Http\Controllers\InventoryStockController::class;
        Route::get('inventory_stocks', [$controller, 'index'])->name('inventory_stocks.index');
        Route::get('inventory_stocks/data', [$controller, 'data'])->name('inventory_stocks.data');
        Route::get('inventory_stocks/create', [$controller, 'create'])->name('inventory_stocks.create');
        Route::post('inventory_stocks', [$controller, 'store'])->name('inventory_stocks.store');
        Route::get('inventory_stocks/{slug}/edit', [$controller, 'edit'])->name('inventory_stocks.edit');
        Route::put('inventory_stocks/{slug}', [$controller, 'update'])->name('inventory_stocks.update');
        Route::delete('inventory_stocks/{slug}', [$controller, 'destroy'])->name('inventory_stocks.destroy');
    });

<?php

use Illuminate\Support\Facades\Route;
use Modules\Audit\Http\Controllers\AuditTrailController;

/*
|--------------------------------------------------------------------------
| Routes modul Audit
|--------------------------------------------------------------------------
| Route didaftarkan sendiri oleh modul sehingga bersifat mandiri. Nama route
| tetap mengikuti konvensi inti: admin.audit.*
*/

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        $controller = AuditTrailController::class;

        Route::get('audit-trail', [$controller, 'index'])->name('audit.index');
        Route::get('audit-trail/data', [$controller, 'data'])->name('audit.data');
        Route::get('audit-trail/{id}', [$controller, 'show'])->name('audit.show');
    });

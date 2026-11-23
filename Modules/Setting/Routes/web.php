<?php

use Illuminate\Support\Facades\Route;
use Modules\Setting\Http\Controllers\SettingAppController;

/*
|--------------------------------------------------------------------------
| Routes modul Setting
|--------------------------------------------------------------------------
| Form pengaturan aplikasi (tunggal). Nama route tetap admin.setting-app.*
*/

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('setting-aplikasi', [SettingAppController::class, 'edit'])->name('setting-app.index');
        Route::post('setting-aplikasi', [SettingAppController::class, 'update'])->name('setting-app.update');
    });

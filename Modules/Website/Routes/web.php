<?php

use Illuminate\Support\Facades\Route;
use Modules\Website\Http\Controllers\WebsiteController;

/*
|--------------------------------------------------------------------------
| Routes modul Website (admin)
|--------------------------------------------------------------------------
| Hanya route admin pengelolaan website/frontpage yang didaftarkan di sini.
| Route frontpage publik (termasuk catch-all `{mitraSlug}`) tetap berada di
| routes/web.php inti.
| Nama route tetap mengikuti konvensi inti: admin.website.* / admin.mitra_website.*
*/

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        $controller = WebsiteController::class;

        // Kelola website/frontpage platform (Principal).
        Route::get('website', [$controller, 'platform'])->name('website.index');
        Route::post('website', [$controller, 'savePlatform'])->name('website.save');

        // Kelola website/frontpage mitra aktif.
        Route::get('mitra-website', [$controller, 'mitra'])->name('mitra_website.index');
        Route::post('mitra-website', [$controller, 'saveMitra'])->name('mitra_website.save');
    });

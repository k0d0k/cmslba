<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\PreferenceController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\MitraRegistrationController;
use Illuminate\Support\Facades\Route;
use Modules\Website\Http\Controllers\FrontpageController;

/*
|--------------------------------------------------------------------------
| Publik
|--------------------------------------------------------------------------
*/
// Frontpage platform (landing page publik).
Route::get('/', [FrontpageController::class, 'platform'])->name('home');

Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::get('login/expired', [AuthController::class, 'showExpired'])->name('login.expired');
Route::post('login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Pendaftaran mitra (tenant) publik
Route::get('register', [MitraRegistrationController::class, 'show'])->name('mitra.register');
Route::post('register', [MitraRegistrationController::class, 'register'])->name('mitra.register.store');

/*
|--------------------------------------------------------------------------
| Admin (butuh login + konteks mitra/tenant)
|--------------------------------------------------------------------------
| Route fitur (Dashboard, Multi Mitra, Cabang, Pelanggan, Website, Brand,
| Registry Modul, Paket Langganan, User, SINA AI, Metode Pembayaran,
| Setting, Menu Dinamis, Role & Akses, CRUD Generator, Jejak Audit) 
| didaftarkan oleh masing-masing modul di Modules/<Nama>/Routes/web.php.
| Di sini hanya route inti yang belum dimiliki modul.
*/
Route::middleware(['auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Preferensi: bahasa, skin, mitra & cabang aktif
        Route::post('preference/locale', [PreferenceController::class, 'locale'])->name('preference.locale');
        Route::post('preference/skin', [PreferenceController::class, 'skin'])->name('preference.skin');
        Route::post('preference/mitra', [PreferenceController::class, 'switchMitra'])->name('preference.mitra');
        Route::post('preference/cabang', [PreferenceController::class, 'switchCabang'])->name('preference.cabang');

        // Akun: profil & ganti password
        Route::get('account/profile', [AccountController::class, 'profile'])->name('account.profile');
        Route::post('account/profile', [AccountController::class, 'updateProfile'])->name('account.profile.update');
        Route::get('account/password', [AccountController::class, 'password'])->name('account.password');
        Route::post('account/password', [AccountController::class, 'updatePassword'])->name('account.password.update');
    });

/*
|--------------------------------------------------------------------------
| Frontpage mitra: http://domain/{nama-mitra}   (HARUS paling bawah)
|--------------------------------------------------------------------------
*/
Route::get('{mitraSlug}', [FrontpageController::class, 'mitra'])
    ->where('mitraSlug', '[a-z0-9\-]+')
    ->name('frontpage.mitra');

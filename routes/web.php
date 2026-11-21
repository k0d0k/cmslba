<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\CrudGeneratorController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\MitraController;
use App\Http\Controllers\Admin\PreferenceController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\FrontpageController;
use App\Http\Controllers\MitraRegistrationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Publik
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('login');
});

Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Pendaftaran mitra (tenant) publik
Route::get('register', [MitraRegistrationController::class, 'show'])->name('mitra.register');
Route::post('register', [MitraRegistrationController::class, 'register'])->name('mitra.register.store');

/*
|--------------------------------------------------------------------------
| Admin (butuh login + konteks mitra/tenant)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

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

        // Manajemen menu dinamis (drag & drop)
        Route::middleware('role:admin_principal')->group(function () {
            Route::get('menus', [MenuController::class, 'index'])->name('menus.index');
            Route::post('menus', [MenuController::class, 'store'])->name('menus.store');
            Route::put('menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
            Route::delete('menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');
            Route::post('menus/reorder', [MenuController::class, 'reorder'])->name('menus.reorder');

            // Assign menu + permission CRUD per role
            Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
            Route::get('roles/{role}/assign', [RoleController::class, 'assign'])->name('roles.assign');
            Route::post('roles/{role}/assign', [RoleController::class, 'saveAssign'])->name('roles.assign.save');

            // Multi mitra
            Route::get('mitras', [MitraController::class, 'index'])->name('mitras.index');
            Route::get('mitras/data', [MitraController::class, 'data'])->name('mitras.data');
            Route::get('mitras/create', [MitraController::class, 'create'])->name('mitras.create');
            Route::post('mitras', [MitraController::class, 'store'])->name('mitras.store');
            Route::get('mitras/{mitra}/edit', [MitraController::class, 'edit'])->name('mitras.edit');
            Route::put('mitras/{mitra}', [MitraController::class, 'update'])->name('mitras.update');
            Route::delete('mitras/{mitra}', [MitraController::class, 'destroy'])->name('mitras.destroy');

            // CRUD Generator
            Route::get('generator', [CrudGeneratorController::class, 'index'])->name('generator.index');
            Route::get('generator/create', [CrudGeneratorController::class, 'create'])->name('generator.create');
            Route::post('generator', [CrudGeneratorController::class, 'store'])->name('generator.store');
        });
    });

/*
|--------------------------------------------------------------------------
| Frontpage mitra: http://domain/{nama-mitra}   (HARUS paling bawah)
|--------------------------------------------------------------------------
*/
Route::get('{mitraSlug}', [FrontpageController::class, 'show'])
    ->where('mitraSlug', '[a-z0-9\-]+')
    ->name('frontpage.mitra');

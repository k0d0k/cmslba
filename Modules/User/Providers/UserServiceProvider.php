<?php

namespace Modules\User\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * ServiceProvider modul User (Modular Native).
 *
 * Modul ini mendaftarkan sendiri route-nya (Routes/web.php) dan migrasi
 * pusatnya (Database/Migrations). Modul ini tidak memiliki view sendiri —
 * controller memakai view CRUD bersama ('admin.crud').
 */
class UserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}

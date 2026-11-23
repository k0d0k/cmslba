<?php

namespace Modules\Brand\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * ServiceProvider modul Brand (Modular Native).
 *
 * Modul ini mendaftarkan sendiri route-nya (Routes/web.php) dan migrasi
 * pusatnya (Database/Migrations). Modul ini tidak memiliki view tersendiri;
 * CRUD memakai view bersama `admin.crud`.
 */
class BrandServiceProvider extends ServiceProvider
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

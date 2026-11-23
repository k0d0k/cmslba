<?php

namespace Modules\Website\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * ServiceProvider modul Website (Modular Native).
 *
 * Modul ini mendaftarkan sendiri route admin-nya (Routes/web.php), view-nya
 * (Resources/views → namespace "website") dan migrasi pusatnya
 * (Database/Migrations, dijalankan pada koneksi `central`).
 *
 * Route frontpage publik (termasuk catch-all `{mitraSlug}`) tetap berada di
 * routes/web.php inti dan memakai Modules\Website\Http\Controllers\FrontpageController.
 */
class WebsiteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'website');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}

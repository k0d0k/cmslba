<?php

namespace Modules\Events\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * ServiceProvider modul Events (Modular Native).
 *
 * Modul ini mendaftarkan sendiri route-nya (Routes/web.php) dan view-nya
 * (Resources/views) sehingga bersifat mandiri. Migrasi modul berada di
 * Modules/Events/Database/Migrations/Tenant dan dijalankan ke database mitra
 * (tenant) oleh App\Services\TenantService.
 */
class EventsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'events');
    }
}

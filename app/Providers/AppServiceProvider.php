<?php

namespace App\Providers;

use App\Models\Menu;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->registerGeneratedCrudRoutes();
    }

    /**
     * Daftarkan route CRUD hasil generator secara dinamis.
     * Semua menu dengan kolom `resource` akan otomatis punya route admin/<key>.
     */
    protected function registerGeneratedCrudRoutes(): void
    {
        try {
            if (! \Illuminate\Support\Facades\Schema::connection('central')->hasTable('menus')) {
                return;
            }
            $menus = Menu::whereNotNull('resource')->where('is_active', true)->get();
        } catch (\Throwable $e) {
            return;
        }

        Route::middleware(['web', 'auth', 'tenant'])
            ->prefix('admin')
            ->name('admin.')
            ->group(function () use ($menus) {
                foreach ($menus as $menu) {
                    $controller = 'App\\Http\\Controllers\\Admin\\' . $menu->resource . 'Controller';
                    if (! class_exists($controller)) {
                        continue;
                    }

                    $key = $menu->key;
                    Route::get($key, [$controller, 'index'])->name("{$key}.index");
                    Route::get($key . '/data', [$controller, 'data'])->name("{$key}.data");
                    Route::get($key . '/create', [$controller, 'create'])->name("{$key}.create");
                    Route::post($key, [$controller, 'store'])->name("{$key}.store");
                    Route::get($key . '/{slug}/edit', [$controller, 'edit'])->name("{$key}.edit");
                    Route::put($key . '/{slug}', [$controller, 'update'])->name("{$key}.update");
                    Route::delete($key . '/{slug}', [$controller, 'destroy'])->name("{$key}.destroy");
                }
            });
    }
}

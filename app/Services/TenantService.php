<?php

namespace App\Services;

use Modules\Mitra\Models\Mitra;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * Menangani database terpisah untuk setiap mitra (multi-tenant).
 *
 * Produksi (MySQL) : membuat database lbacloud_mitra_<uuid>.
 * Sandbox (SQLite) : membuat file database/tenants/lbacloud_mitra_<uuid>.sqlite.
 */
class TenantService
{
    public function path(): string
    {
        return config('lbacloud.tenant_migration_path');
    }

    /**
     * Semua path migrasi untuk database mitra: folder inti (database/migrations/tenant)
     * digabung dengan subfolder `Tenant` milik setiap modul di Modules/.
     *
     * Konvensi Modular Native:
     *   Modules/<Nama>/Database/Migrations/*.php         -> migrasi pusat (central)
     *   Modules/<Nama>/Database/Migrations/Tenant/*.php  -> migrasi database mitra (tenant)
     */
    public function migrationPaths(): array
    {
        $paths = [];

        // Folder migrasi tenant inti (bila ada).
        $base = (string) $this->path();
        if (is_dir($base)) {
            $paths[] = $base;
        }

        // Migrasi tenant milik setiap modul.
        $moduleMigrations = glob(config('lbacloud.modules_path') . '/*/Database/Migrations/Tenant', GLOB_ONLYDIR) ?: [];
        foreach ($moduleMigrations as $dir) {
            $paths[] = $dir;
        }

        return array_values(array_unique($paths));
    }

    public function sqlitePath(Mitra $mitra): string
    {
        return database_path('tenants/' . $mitra->db_name . '.sqlite');
    }

    /** Aktifkan koneksi `tenant` ke database milik mitra tertentu. */
    public function connect(Mitra $mitra): void
    {
        $driver = config('database.connections.tenant.driver');

        $database = $driver === 'sqlite'
            ? $this->ensureSqlite($mitra)
            : $mitra->db_name;

        Config::set('database.connections.tenant.database', $database);
        DB::purge('tenant');
    }

    public function disconnect(): void
    {
        Config::set('database.connections.tenant.database', null);
        DB::purge('tenant');
    }

    protected function ensureSqlite(Mitra $mitra): string
    {
        $dir = database_path('tenants');
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $path = $this->sqlitePath($mitra);
        if (! file_exists($path)) {
            touch($path);
        }

        return $path;
    }

    /**
     * Provisioning mitra baru: buat database + jalankan migrasi khusus mitra.
     */
    public function provision(Mitra $mitra): void
    {
        $driver = config('database.connections.tenant.driver');

        if ($driver !== 'sqlite') {
            DB::connection('central')->statement(
                "CREATE DATABASE IF NOT EXISTS `{$mitra->db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            );
        }

        $this->connect($mitra);

        Artisan::call('migrate', [
            '--path'     => $this->migrationPaths(),
            '--realpath' => true,
            '--database' => 'tenant',
            '--force'    => true,
        ]);
    }

    /** Hapus database mitra (dipakai saat mitra dihapus). */
    public function drop(Mitra $mitra): void
    {
        $driver = config('database.connections.tenant.driver');

        if ($driver === 'sqlite') {
            $path = $this->sqlitePath($mitra);
            if (file_exists($path)) {
                @unlink($path);
            }
        } else {
            DB::connection('central')->statement("DROP DATABASE IF EXISTS `{$mitra->db_name}`");
        }
    }
}

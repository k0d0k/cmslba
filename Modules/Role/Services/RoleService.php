<?php

namespace Modules\Role\Services;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

/**
 * Layanan untuk membaca meta role (label, scope, description).
 * Sumber: config/lbacloud.php (role bawaan) + tabel settings group 'roles_meta' (role custom).
 */
class RoleService
{
    /**
     * Ambil seluruh meta role: [name => ['label','scope','description']]
     * menggabungkan config dan settings (role custom).
     */
    public function allMeta(): array
    {
        $config = config('lbacloud.roles', []);

        $custom = DB::connection('central')->table('settings')
            ->where('group', 'roles_meta')
            ->get()
            ->mapWithKeys(function ($row) {
                $name = str_replace('roles_meta.', '', $row->key);

                return [$name => json_decode($row->value, true) ?: []];
            })->all();

        // Role custom tidak menimpa config bawaan.
        return array_merge($custom, $config);
    }

    /** Meta satu role. */
    public function meta(string $name): ?array
    {
        return $this->allMeta()[$name] ?? null;
    }

    /**
     * Daftar label role: [name => label].
     * Urut sesuai urutan di tabel roles.
     */
    public function labels(): array
    {
        $meta = $this->allMeta();

        return Role::query()
            ->orderBy('name')
            ->pluck('name')
            ->mapWithKeys(fn ($name) => [$name => $meta[$name]['label'] ?? $name])
            ->all();
    }

    /** Label satu role (fallback ke nama role). */
    public function label(string $name): string
    {
        return $this->meta($name)['label'] ?? $name;
    }
}

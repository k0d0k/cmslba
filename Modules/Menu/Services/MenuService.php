<?php

namespace Modules\Menu\Services;

use Modules\Menu\Models\Menu;
use Modules\Subscription\Models\Subscription;
use Modules\User\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

/**
 * Logika menu dinamis + hak akses (assign menu & CRUD per role).
 */
class MenuService
{
    public const ABILITIES = ['view', 'create', 'update', 'delete', 'export'];

    /** Ambil ability CRUD user pada sebuah menu. */
    public function abilities(User $user, Menu $menu): array
    {
        if ($user->hasRole('admin_principal')) {
            return self::ABILITIES;
        }

        // Batasan modul: menu yang modulnya tidak ada di paket langganan mitra
        // tidak bisa diakses (sidebar maupun URL langsung).
        if (! $this->menuWithinLicense($user, $menu)) {
            return [];
        }

        $roleIds = $user->roles->pluck('id')->all();
        if (empty($roleIds)) {
            return [];
        }

        $rows = DB::connection('central')->table('role_menu')
            ->whereIn('role_id', $roleIds)
            ->where('menu_id', $menu->id)
            ->get();

        $abilities = [];
        foreach (self::ABILITIES as $ability) {
            if ($rows->contains(fn ($r) => (bool) $r->{'can_' . $ability})) {
                $abilities[] = $ability;
            }
        }

        return $abilities;
    }

    public function userCan(User $user, Menu $menu, string $ability = 'view'): bool
    {
        return in_array($ability, $this->abilities($user, $menu), true);
    }

    /**
     * Kode modul yang boleh diakses user.
     * - null  : tanpa batasan (principal / user pusat).
     * - array : daftar kode modul dari paket langganan aktif mitra (bisa kosong
     *           bila mitra belum/tidak punya langganan aktif).
     */
    public function licensedModuleKeys(?User $user): ?array
    {
        if (! $user) {
            return [];
        }

        if ($user->hasRole('admin_principal') || $user->isPrincipal()) {
            return null;
        }

        if (! $user->mitra_id) {
            return null;
        }

        $subscription = $user->mitra?->activeSubscription();
        $keys = $subscription?->subscription?->module_keys;

        return is_array($keys) ? $keys : [];
    }

    /** Apakah menu berada dalam cakupan modul yang dilanggan user. */
    public function menuWithinLicense(?User $user, Menu $menu): bool
    {
        $licensed = $this->licensedModuleKeys($user);
        if ($licensed === null) {
            return true; // tanpa batasan
        }

        if (blank($menu->module_key)) {
            return true; // menu sistem selalu tampil
        }

        return in_array($menu->module_key, $licensed, true);
    }

    /**
     * Beri role default mitra (pemilik_mitra) kemampuan penuh (CRUD) untuk
     * seluruh menu yang termasuk modul pada paket langganan tertentu.
     * Dipanggil saat paket mitra dibuat/dipasang.
     */
    public function grantPackageMenusToDefaultMitraRole(Subscription $package): void
    {
        $keys = $package->module_keys ?? [];
        if (empty($keys)) {
            return;
        }

        foreach (Menu::whereIn('module_key', $keys)->get() as $menu) {
            $this->grantMenuToDefaultMitraRole($menu);
        }
    }

    /**
     * Beri role default mitra (pemilik_mitra) kemampuan penuh (CRUD) atas satu
     * menu ber-modul, agar menu itu tampil untuk mitra yang paketnya memuat modul.
     * Menu sistem (tanpa modul) tidak diubah — hak aksesnya tetap dari role masing-masing.
     */
    public function grantMenuToDefaultMitraRole(Menu $menu): void
    {
        if (blank($menu->module_key)) {
            return;
        }

        $role = Role::where('name', 'pemilik_mitra')->first();
        if (! $role) {
            return;
        }

        $this->assign($role, $menu->id, self::ABILITIES);
    }

    /** Ability untuk menu berdasarkan route name (dipakai controller). */
    public function userCanRoute(User $user, string $routeName, string $ability = 'view'): bool
    {
        $menu = Menu::where('route_name', $routeName)->first();
        if (! $menu) {
            return $user->hasRole('admin_principal');
        }

        return $this->userCan($user, $menu, $ability);
    }

    /**
     * Sidebar: daftar menu (tree) yang boleh dilihat user.
     */
    public function sidebar(User $user): Collection
    {
        $menus = Menu::active()->where('is_visible', true)->orderBy('sort')->get();

        $visible = $menus->filter(fn (Menu $m) => $this->userCan($user, $m, 'view'));

        // Sertakan parent yang dibutuhkan (group/header) walau anaknya yang punya akses.
        $withParents = collect($visible);
        foreach ($visible as $m) {
            $parentId = $m->parent_id;
            while ($parentId) {
                $parent = $menus->firstWhere('id', $parentId);
                if (! $parent) {
                    break;
                }
                if (! $withParents->contains('id', $parent->id)) {
                    $withParents->push($parent);
                }
                $parentId = $parent->parent_id;
            }
        }

        return $this->buildTree($withParents, null);
    }

    public function buildTree(Collection $menus, ?int $parentId): Collection
    {
        return $menus
            ->where('parent_id', $parentId)
            ->sortBy('sort')
            ->values()
            ->map(function (Menu $menu) use ($menus) {
                $children = $this->buildTree($menus, $menu->id);
                $menu->setAttribute('children_tree', $children);

                return $menu;
            });
    }

    /** Simpan urutan & struktur menu dari hasil drag & drop. */
    public function saveOrder(array $items, ?int $parentId = null): void
    {
        foreach (array_values($items) as $index => $item) {
            $id = $item['id'] ?? null;
            if (! $id) {
                continue;
            }

            $menu = Menu::find($id);
            if (! $menu) {
                continue;
            }

            $menu->parent_id = $parentId;
            $menu->sort = $index;
            $menu->save();

            if (! empty($item['children'])) {
                $this->saveOrder($item['children'], $menu->id);
            }
        }
    }

    /** Assign daftar kemampuan CRUD role pada sebuah menu. */
    public function assign(Role $role, int $menuId, array $abilities): void
    {
        $data = [
            'can_view'   => in_array('view', $abilities, true),
            'can_create' => in_array('create', $abilities, true),
            'can_update' => in_array('update', $abilities, true),
            'can_delete' => in_array('delete', $abilities, true),
            'can_export' => in_array('export', $abilities, true),
            'updated_at' => now(),
            'created_at' => now(),
        ];

        DB::connection('central')->table('role_menu')->updateOrInsert(
            ['role_id' => $role->id, 'menu_id' => $menuId],
            $data,
        );

        // Sinkronkan juga ke spatie permission (menu.{key}.{ability}).
        $menu = Menu::find($menuId);
        if ($menu) {
            foreach ($abilities as $ability) {
                $perm = "menu.{$menu->key}.{$ability}";
                \Spatie\Permission\Models\Permission::findOrCreate($perm, 'web');
                $role->givePermissionTo($perm);
            }
        }
    }

    public function revoke(Role $role, int $menuId): void
    {
        DB::connection('central')->table('role_menu')
            ->where('role_id', $role->id)
            ->where('menu_id', $menuId)
            ->delete();
    }
}

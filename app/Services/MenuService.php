<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\User;
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Services\MenuService;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct(
        protected MenuService $menuService,
        protected RoleService $roleService,
    ) {
    }

    public function index()
    {
        $roles = Role::withCount('users')->orderBy('name')->get();
        $config = $this->roleService->allMeta();

        // Jumlah menu yang di-assign per role.
        $menuCount = DB::connection('central')->table('role_menu')
            ->select('role_id', DB::raw('COUNT(*) as total'))
            ->groupBy('role_id')
            ->pluck('total', 'role_id')
            ->all();

        return view('admin.roles.index', compact('roles', 'config', 'menuCount'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|alpha_dash|unique:roles,name',
            'label' => 'required|string|max:150',
            'scope' => 'required|in:principal,mitra',
            'description' => 'nullable|string|max:255',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        // Simpan meta role (label, scope, description) ke config runtime via cache?
        // Karena config 'lbacloud.roles' bersifat statis di file, kita simpan
        // ke tabel roles? Tidak. Spatie Role tidak punya kolom label.
        // Alternatif: simpan di settings tabel (group roles).
        DB::connection('central')->table('settings')->updateOrInsert(
            ['key' => 'roles_meta.' . $role->name, 'group' => 'roles_meta'],
            [
                'value' => json_encode([
                    'label' => $validated['label'],
                    'scope' => $validated['scope'],
                    'description' => $validated['description'] ?? null,
                ]),
                'updated_at' => now(),
                'created_at' => now(),
            ],
        );

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role "' . $validated['label'] . '" berhasil ditambahkan.');
    }

    public function assign(Role $role)
    {
        $menus = Menu::orderBy('sort')->get();
        $tree = $this->menuService->buildTree($menus, null);

        // Peta menu_id => abilities untuk role ini
        $assigned = DB::connection('central')->table('role_menu')
            ->where('role_id', $role->id)
            ->get()
            ->mapWithKeys(function ($row) {
                $abilities = [];
                foreach (MenuService::ABILITIES as $a) {
                    if ((bool) $row->{'can_' . $a}) {
                        $abilities[] = $a;
                    }
                }

                return [$row->menu_id => $abilities];
            })->all();

        return view('admin.roles.assign', [
            'role'     => $role,
            'tree'     => $tree,
            'assigned' => $assigned,
            'abilities' => MenuService::ABILITIES,
        ]);
    }

    public function saveAssign(Request $request, Role $role)
    {
        $payload = $request->input('menus', []); // [menu_id => [abilities...]]

        // Reset
        DB::connection('central')->table('role_menu')->where('role_id', $role->id)->delete();

        foreach ($payload as $menuId => $abilities) {
            $this->menuService->assign($role, (int) $menuId, (array) $abilities);
        }

        return redirect()->route('admin.roles.index')->with('success', __('messages.updated'));
    }
}

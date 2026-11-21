<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Services\MenuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct(protected MenuService $menuService)
    {
    }

    public function index()
    {
        $roles = Role::withCount('users')->orderBy('name')->get();
        $config = config('lbacloud.roles');

        return view('admin.roles.index', compact('roles', 'config'));
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

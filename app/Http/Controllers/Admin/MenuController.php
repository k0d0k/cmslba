<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Services\MenuService;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function __construct(protected MenuService $menuService)
    {
    }

    public function index(Request $request)
    {
        $menus = Menu::orderBy('sort')->get();
        $tree = $this->menuService->buildTree($menus, null);

        $edit = null;
        if ($request->filled('edit')) {
            $edit = Menu::where('key', $request->query('edit'))->first();
        }

        return view('admin.menus.index', [
            'tree'    => $tree,
            'parents' => $menus->whereNull('parent_id'),
            'edit'    => $edit,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Menu::create($data);

        return redirect()->route('admin.menus.index')->with('success', __('messages.created'));
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $this->validated($request, $menu->id);
        $menu->update($data);

        return redirect()->route('admin.menus.index')->with('success', __('messages.updated'));
    }

    public function destroy(Menu $menu)
    {
        $menu->children()->update(['parent_id' => $menu->parent_id]);
        $menu->delete();

        return back()->with('success', __('messages.deleted'));
    }

    /** Simpan urutan hasil drag & drop (nested). */
    public function reorder(Request $request)
    {
        $tree = $request->input('tree', []);
        $this->menuService->saveOrder($tree);

        return response()->json(['ok' => true]);
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        $request->validate([
            'title_id' => ['required', 'string', 'max:100'],
            'parent_id' => ['nullable', 'integer', 'exists:menus,id'],
            'type'     => ['required', 'in:group,link,header'],
        ]);

        return [
            'title'      => [
                'id' => $request->input('title_id'),
                'en' => $request->input('title_en', $request->input('title_id')),
                'ms' => $request->input('title_ms', $request->input('title_id')),
            ],
            'parent_id'  => $request->input('parent_id'),
            'icon'       => $request->input('icon', 'circle'),
            'type'       => $request->input('type'),
            'route_name' => $request->input('route_name'),
            'url'        => $request->input('url'),
            'target'     => $request->input('target', '_self'),
            'sort'       => (int) $request->input('sort', 0),
            'is_active'  => $request->boolean('is_active', true),
            'is_visible' => $request->boolean('is_visible', true),
        ];
    }
}

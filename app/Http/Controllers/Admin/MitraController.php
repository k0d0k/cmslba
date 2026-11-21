<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Menu;
use App\Models\Mitra;
use App\Models\Subscription;
use App\Services\MenuService;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MitraController extends Controller
{
    public function __construct(protected TenantService $tenant)
    {
    }

    /** Cek hak akses aksi berdasarkan menu `mitras` di database pusat. */
    protected function can(string $ability): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }
        if ($user->hasRole('admin_principal')) {
            return true;
        }
        $menu = Menu::where('key', 'mitras')->first();

        return $menu ? app(MenuService::class)->userCan($user, $menu, $ability) : false;
    }

    protected function authorizeAbility(string $ability): void
    {
        abort_unless($this->can($ability), 403, __('messages.forbidden'));
    }

    public function index()
    {
        $this->authorizeAbility('view');

        return view('admin.mitras.index', [
            'canCreate' => $this->can('create'),
            'canUpdate' => $this->can('update'),
            'canDelete' => $this->can('delete'),
        ]);
    }

    public function data()
    {
        $this->authorizeAbility('view');

        $query = Mitra::query()->with(['brand', 'langganan']);

        // Selain principal: hanya boleh melihat mitranya sendiri.
        $user = auth()->user();
        if ($user && ! $user->isPrincipal()) {
            $query->whereKey($user->mitra_id);
        }

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->editColumn('status', fn ($m) => '<span class="badge badge-' . ($m->status === 'active' ? 'success' : 'warning') . '">' . $m->status . '</span>')
            ->addColumn('brand', fn ($m) => optional($m->brand)->name)
            ->addColumn('langganan', fn ($m) => optional($m->langganan)->name)
            ->addColumn('actions', function ($m) {
                $html = '<div class="flex gap-2">';
                if ($this->can('update')) {
                    $html .= '<a href="' . route('admin.mitras.edit', $m->slug) . '" class="btn btn-sm btn-primary">' . __('common.edit') . '</a>';
                }
                if ($this->can('delete')) {
                    $html .= '<button type="button" class="btn btn-sm btn-danger js-delete" data-url="' . route('admin.mitras.destroy', $m->slug) . '">' . __('common.delete') . '</button>';
                }
                $html .= '</div>';

                return $html;
            })
            ->rawColumns(['status', 'actions'])
            ->toJson();
    }

    public function create()
    {
        $this->authorizeAbility('create');

        return view('admin.mitras.form', [
            'mitra'         => null,
            'brands'        => Brand::orderBy('name')->get(),
            'subscriptions' => Subscription::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeAbility('create');
        $data = $this->validated($request);
        $mitra = Mitra::create($data);

        $this->tenant->provision($mitra);
        $this->seedTenant($mitra);

        return redirect()->route('admin.mitras.index')
            ->with('success', __('messages.mitra_created', ['name' => $mitra->name, 'db' => $mitra->db_name]));
    }

    public function edit(Mitra $mitra)
    {
        $this->authorizeAbility('update');

        return view('admin.mitras.form', [
            'mitra'         => $mitra,
            'brands'        => Brand::orderBy('name')->get(),
            'subscriptions' => Subscription::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Mitra $mitra)
    {
        $this->authorizeAbility('update');
        $mitra->update($this->validated($request, $mitra->id));

        return redirect()->route('admin.mitras.index')->with('success', __('messages.updated'));
    }

    public function destroy(Mitra $mitra)
    {
        $this->authorizeAbility('delete');
        $this->tenant->drop($mitra);
        $mitra->delete();

        if (request()->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', __('messages.deleted'));
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name'   => ['required', 'string', 'max:150'],
            'slug'   => ['nullable', 'string', 'max:150', 'unique:mitras,slug' . ($ignoreId ? ",{$ignoreId}" : '')],
            'email'  => ['nullable', 'email'],
            'phone'  => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'skin'   => ['nullable', 'string'],
            'locale' => ['nullable', 'in:id,en,ms'],
            'status' => ['required', 'in:pending,active,suspended'],
            'plan'   => ['nullable', 'string'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'langganan_id' => ['nullable', 'exists:subscriptions,id'],
        ]);
    }

    protected function seedTenant(Mitra $mitra): void
    {
        $this->tenant->connect($mitra);

        DB::connection('tenant')->table('cabangs')->insert([
            'name'       => 'Cabang Pusat',
            'slug'       => 'cabang-pusat',
            'code'       => 'PST',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

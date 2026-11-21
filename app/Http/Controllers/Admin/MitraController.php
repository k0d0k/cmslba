<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mitra;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MitraController extends Controller
{
    public function __construct(protected TenantService $tenant)
    {
    }

    public function index()
    {
        return view('admin.mitras.index');
    }

    public function data()
    {
        $query = Mitra::query();

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->editColumn('status', fn ($m) => '<span class="badge badge-' . ($m->status === 'active' ? 'success' : 'warning') . '">' . $m->status . '</span>')
            ->addColumn('actions', function ($m) {
                return '<div class="flex gap-2">'
                    . '<a href="' . route('admin.mitras.edit', $m->slug) . '" class="btn btn-sm btn-primary">' . __('common.edit') . '</a>'
                    . '<button type="button" class="btn btn-sm btn-danger js-delete" data-url="' . route('admin.mitras.destroy', $m->slug) . '">' . __('common.delete') . '</button>'
                    . '</div>';
            })
            ->rawColumns(['status', 'actions'])
            ->toJson();
    }

    public function create()
    {
        return view('admin.mitras.form', ['mitra' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $mitra = Mitra::create($data);

        $this->tenant->provision($mitra);
        $this->seedTenant($mitra);

        return redirect()->route('admin.mitras.index')
            ->with('success', __('messages.mitra_created', ['name' => $mitra->name, 'db' => $mitra->db_name]));
    }

    public function edit(Mitra $mitra)
    {
        return view('admin.mitras.form', compact('mitra'));
    }

    public function update(Request $request, Mitra $mitra)
    {
        $mitra->update($this->validated($request, $mitra->id));

        return redirect()->route('admin.mitras.index')->with('success', __('messages.updated'));
    }

    public function destroy(Mitra $mitra)
    {
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

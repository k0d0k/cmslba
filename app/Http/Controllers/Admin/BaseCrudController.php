<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Services\MenuService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

/**
 * Basis CRUD generik (server-side DataTables + form dinamis).
 * Semua resource hasil CRUD Generator cukup mengextends class ini.
 */
abstract class BaseCrudController extends Controller
{
    protected string $model;
    protected string $view = 'admin.crud';
    protected string $routeName;
    protected string $title = 'Data';
    protected string $menuKey = '';
    protected array $fields = [];
    protected bool $hasCabang = false;

    protected function menuService(): MenuService
    {
        return app(MenuService::class);
    }

    /**
     * Definisi field form/tabel. Bisa dioverride subclass untuk menyisipkan
     * field dinamis (mis. pilihan mitra yang bergantung user yang login).
     */
    protected function fields(): array
    {
        return $this->fields;
    }

    protected function user()
    {
        return auth()->user();
    }

    /** Nama kolom yang dipakai sebagai key pada route (default `slug`, bisa `id` bila tanpa slug). */
    protected function routeKeyName(): string
    {
        return (new ($this->model))->getRouteKeyName();
    }

    protected function can(string $ability): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }
        if ($user->hasRole('admin_principal')) {
            return true;
        }
        $menu = Menu::where('key', $this->menuKey)->first();

        return $menu ? $this->menuService()->userCan($user, $menu, $ability) : false;
    }

    protected function authorizeAbility(string $ability): void
    {
        abort_unless($this->can($ability), 403, __('messages.forbidden'));
    }

    protected function query()
    {
        $query = ($this->model)::query();

        $relations = collect($this->fields())
            ->pluck('relation.model')
            ->filter()
            ->map(fn ($m) => class_basename($m))
            ->all();
        if ($relations) {
            $query->with($relations);
        }

        $user = $this->user();
        if ($this->hasCabang) {
            // Utamakan cabang yang dipilih di topbar; fallback ke cabang bawaan user mitra.
            $cabangId = session('active_cabang_id');
            if (! $cabangId && $user && ! $user->isPrincipal()) {
                $cabangId = $user->cabang_id;
            }
            if ($cabangId) {
                $query->where('cabang_id', $cabangId);
            }
        }

        return $query;
    }

    protected function rules(?int $ignoreId = null): array
    {
        $rules = [];
        foreach ($this->fields() as $f) {
            $type = $f['type'] ?? 'string';
            $name = $f['name'];
            $r = [];
            $r[] = ($f['nullable'] ?? false) ? 'nullable' : 'required';

            $r[] = match ($type) {
                'integer'  => 'integer',
                'decimal'  => 'numeric',
                'boolean'  => 'boolean',
                'date'     => 'date',
                'datetime' => 'date',
                'email'    => 'email',
                'image'    => 'image|max:2048',
                'select'   => 'in:' . implode(',', array_keys($f['options'] ?? [])),
                'text'     => 'string',
                default    => 'string|max:255',
            };

            if (! empty($f['unique'])) {
                $table = (new ($this->model))->getTable();
                $rule = 'unique:' . $table . ',' . $name;
                if ($ignoreId) {
                    $rule .= ',' . $ignoreId;
                }
                $r[] = $rule;
            }

            $rules[$name] = implode('|', $r);
        }

        return $rules;
    }

    protected function values(Request $request): array
    {
        $data = [];
        foreach ($this->fields() as $f) {
            $name = $f['name'];
            $type = $f['type'] ?? 'string';

            if ($type === 'boolean') {
                $data[$name] = $request->boolean($name);
            } elseif ($type === 'image') {
                // Hanya ubah bila ada berkas baru diunggah (agar edit lain tidak menghapus gambar lama).
                if ($request->hasFile($name)) {
                    $data[$name] = $request->file($name)->store('uploads/' . ($this->menuKey ?: 'files'), 'public');
                }
            } else {
                $data[$name] = $request->input($name);
            }
        }

        return $data;
    }

    public function index()
    {
        $this->authorizeAbility('view');

        return view($this->view . '.index', [
            'fields'    => $this->fields(),
            'routeName' => $this->routeName,
            'title'     => $this->title,
            'canCreate' => $this->can('create'),
            'canUpdate' => $this->can('update'),
            'canDelete' => $this->can('delete'),
        ]);
    }

    public function data()
    {
        $this->authorizeAbility('view');

        $dt = DataTables::eloquent($this->query())->addIndexColumn();

        foreach ($this->fields() as $f) {
            $name = $f['name'];
            $type = $f['type'] ?? 'string';

            if ($type === 'boolean') {
                $dt->editColumn($name, fn ($row) => $row->{$name}
                    ? '<span class="badge bg-success">' . __('common.yes') . '</span>'
                    : '<span class="badge bg-secondary">' . __('common.no') . '</span>');
            } elseif ($type === 'image') {
                $dt->editColumn($name, fn ($row) => $row->{$name}
                    ? '<img src="' . e(\Illuminate\Support\Facades\Storage::url($row->{$name})) . '" alt="" class="w-10 h-10 rounded object-cover border border-slate-200">'
                    : '<span class="text-slate-400">-</span>');
            } elseif (in_array($type, ['date', 'datetime'], true)) {
                $dt->editColumn($name, fn ($row) => optional($row->{$name})->format('Y-m-d'));
            } elseif ($type === 'select' && ! empty($f['options'])) {
                $dt->editColumn($name, fn ($row) => ($f['options'][$row->{$name}] ?? $row->{$name}));
            } elseif ($type === 'relation' && ! empty($f['relation'])) {
                $rel = Str::of($name)->replace('_id', '')->camel()->toString();
                $labelCol = $f['relation']['label'] ?? 'name';
                $dt->editColumn($name, fn ($row) => optional($row->{$rel})->{$labelCol});
            }
        }

        $dt->addColumn('actions', function ($row) {
            $html = '<div class="flex gap-2">';
            if ($this->can('update')) {
                $html .= '<a href="' . route($this->routeName . '.edit', $row->getRouteKey()) . '" class="btn btn-sm btn-primary">' . __('common.edit') . '</a>';
            }
            $html .= $this->rowActions($row);
            if ($this->can('delete')) {
                $html .= '<button type="button" class="btn btn-sm btn-danger js-delete" data-url="' . route($this->routeName . '.destroy', $row->getRouteKey()) . '">' . __('common.delete') . '</button>';
            }
            $html .= '</div>';

            return $html;
        });

        $raw = ['actions'];
        foreach ($this->fields() as $f) {
            if (in_array($f['type'] ?? '', ['boolean', 'image'], true)) {
                $raw[] = $f['name'];
            }
        }
        $dt->rawColumns($raw);

        return $dt->toJson();
    }

    /** Aksi tambahan per baris pada tabel (dapat dioverride subclass). */
    protected function rowActions($row): string
    {
        return '';
    }

    public function create()
    {
        $this->authorizeAbility('create');

        return view($this->view . '.form', [
            'fields'    => $this->fields(),
            'routeName' => $this->routeName,
            'title'     => $this->title,
            'record'    => null,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeAbility('create');
        $request->validate($this->rules());

        $data = $this->values($request);
        if ($this->hasCabang && empty($data['cabang_id'])) {
            $data['cabang_id'] = session('active_cabang_id') ?: $this->user()?->cabang_id;
        }

        ($this->model)::create($data);

        return redirect()->route($this->routeName . '.index')
            ->with('success', __('messages.created'));
    }

    public function edit($slug)
    {
        $this->authorizeAbility('update');
        $record = ($this->model)::where($this->routeKeyName(), $slug)->firstOrFail();

        return view($this->view . '.form', [
            'fields'    => $this->fields(),
            'routeName' => $this->routeName,
            'title'     => $this->title,
            'record'    => $record,
        ]);
    }

    public function update(Request $request, $slug)
    {
        $this->authorizeAbility('update');
        $record = ($this->model)::where($this->routeKeyName(), $slug)->firstOrFail();

        $request->validate($this->rules($record->id));
        $record->update($this->values($request));

        return redirect()->route($this->routeName . '.index')
            ->with('success', __('messages.updated'));
    }

    public function destroy($slug)
    {
        $this->authorizeAbility('delete');
        $record = ($this->model)::where($this->routeKeyName(), $slug)->firstOrFail();
        $record->delete();

        if (request()->wantsJson()) {
            return response()->json(['ok' => true, 'message' => __('messages.deleted')]);
        }

        return back()->with('success', __('messages.deleted'));
    }
}

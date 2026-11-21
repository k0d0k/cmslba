<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrudDefinition;
use App\Models\Menu;
use App\Models\Mitra;
use App\Services\CrudGeneratorService;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class CrudGeneratorController extends Controller
{
    public function __construct(
        protected CrudGeneratorService $generator,
        protected TenantService $tenant,
    ) {
    }

    public function index()
    {
        return view('admin.generator.index', [
            'definitions' => CrudDefinition::latest()->get(),
            'menus'       => Menu::where('is_crud', true)->orderBy('sort')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.generator.form', [
            'parents' => Menu::whereNull('parent_id')->orderBy('sort')->get(),
            'roles'   => config('lbacloud.roles'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'          => ['required', 'string', 'max:100'],
            'model'          => ['required', 'string', 'max:60', 'regex:/^[A-Za-z][A-Za-z0-9]+$/'],
            'connection'     => ['required', 'in:central,tenant'],
            'field_name'     => ['required', 'array', 'min:1'],
            'field_name.*'   => ['required', 'string'],
            'field_type.*'   => ['required', 'string'],
        ]);

        $connection = $request->input('connection', 'tenant');
        $hasCabang  = $connection === 'tenant' && $request->boolean('has_cabang');

        $fields = [];
        foreach ($request->input('field_name') as $i => $name) {
            if (blank($name)) {
                continue;
            }
            $type = $request->input("field_type.{$i}", 'string');
            $options = [];
            if ($type === 'select' && $request->filled("field_options.{$i}")) {
                foreach (explode("\n", $request->input("field_options.{$i}")) as $line) {
                    if (str_contains($line, ':')) {
                        [$k, $v] = explode(':', $line, 2);
                        $options[trim($k)] = trim($v);
                    }
                }
            }
            $fields[] = [
                'name'     => $name,
                'type'     => $type,
                'label'    => $request->input("field_label.{$i}") ?: \Illuminate\Support\Str::headline($name),
                'nullable' => (bool) $request->input("field_nullable.{$i}"),
                'unique'   => (bool) $request->input("field_unique.{$i}"),
                'options'  => $options,
            ];
        }

        $result = $this->generator->generate([
            'title'      => $request->input('title'),
            'key'        => $request->input('key'),
            'model'      => $request->input('model'),
            'icon'       => $request->input('icon', 'table'),
            'parent_id'  => $request->input('parent_id') ?: null,
            'has_cabang' => $hasCabang,
            'connection' => $connection,
            'fields'     => $fields,
            'roles'      => $request->input('roles', []),
        ]);

        $this->migrate($connection);

        return redirect()->route('admin.generator.index')
            ->with('success', __('messages.crud_generated', ['model' => $request->input('model')]))
            ->with('generated', $result);
    }

    /**
     * Jalankan migrasi pada database target (pusat atau mitra) yang baru dibuat.
     */
    protected function migrate(string $connection): void
    {
        if ($connection === 'tenant') {
            $mitra = app()->bound('active_mitra') ? app('active_mitra') : Mitra::where('status', 'active')->first();
            if (! $mitra) {
                return;
            }
            $this->tenant->connect($mitra);

            Artisan::call('migrate', [
                '--path'     => $this->tenant->path(),
                '--realpath' => true,
                '--database' => 'tenant',
                '--force'    => true,
            ]);

            return;
        }

        Artisan::call('migrate', [
            '--path'     => database_path('migrations'),
            '--realpath' => true,
            '--database' => 'central',
            '--force'    => true,
        ]);
    }
}

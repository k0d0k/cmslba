@extends('layouts.admin')

@section('title', 'CRUD Generator')

@section('content')
    @if(session('generated'))
        @php $g = session('generated'); @endphp
        <div class="card mb-4">
            <div class="card-header">File yang dibuat</div>
            <div class="card-body text-xs text-slate-600 space-y-1">
                <div><b>Migration:</b> <code>{{ str_replace(base_path() . '/', '', $g['migration']) }}</code></div>
                <div><b>Model:</b> <code>{{ str_replace(base_path() . '/', '', $g['model']) }}</code></div>
                <div><b>Controller:</b> <code>{{ str_replace(base_path() . '/', '', $g['controller']) }}</code></div>
                <div><b>Menu:</b> <code>{{ $g['menu']->key }}</code> &middot; route <code>{{ $g['menu']->route_name }}</code></div>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header flex items-center justify-between">
            <span>Definisi CRUD</span>
            <a href="{{ route('admin.generator.create') }}" class="btn btn-primary btn-sm">Buat CRUD Baru</a>
        </div>
        <div class="card-body p-0">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs uppercase text-slate-500 border-b border-slate-200 bg-slate-50">
                        <th class="px-4 py-3 text-left">Model</th>
                        <th class="px-4 py-3 text-left">Tabel</th>
                        <th class="px-4 py-3 text-left">Koneksi</th>
                        <th class="px-4 py-3 text-left">Menu</th>
                        <th class="px-4 py-3 text-left">Roles</th>
                        <th class="px-4 py-3 text-right">{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($definitions as $def)
                        <tr class="border-b border-slate-100">
                            <td class="px-4 py-3 font-medium text-slate-700">{{ $def->model_name }}</td>
                            <td class="px-4 py-3"><code class="text-xs">{{ $def->table_name }}</code></td>
                            <td class="px-4 py-3"><span class="badge badge-secondary">{{ $def->connection }}</span></td>
                            <td class="px-4 py-3">
                                @if($def->menu)
                                    <code class="text-xs">{{ $def->menu->route_name }}</code>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500">
                                {{ implode(', ', $def->roles ?? []) ?: '-' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if($def->menu)
                                    <a href="{{ route($def->menu->route_name) }}" class="btn btn-secondary btn-sm">{{ __('common.view') }}</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-6 text-center text-slate-400">{{ __('messages.no_data') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

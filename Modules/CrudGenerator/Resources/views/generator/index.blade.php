@extends('layouts.admin')

@section('title', __('generator.title'))

@section('content')
    @if(session('generated'))
        @php $g = session('generated'); @endphp
        <div class="card mb-4">
            <div class="card-header">{{ __('generator.files_created') }}</div>
            <div class="card-body text-xs text-slate-600 space-y-1">
                <div><b>Migration:</b> <code>{{ str_replace(base_path() . '/', '', $g['migration']) }}</code></div>
                <div><b>{{ __('generator.col_model') }}:</b> <code>{{ str_replace(base_path() . '/', '', $g['model']) }}</code></div>
                <div><b>Controller:</b> <code>{{ str_replace(base_path() . '/', '', $g['controller']) }}</code></div>
                <div><b>{{ __('generator.col_menu') }}:</b> <code>{{ $g['menu']->key }}</code> &middot; route <code>{{ $g['menu']->route_name }}</code></div>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header flex items-center justify-between">
            <span>{{ __('generator.definitions') }}</span>
            <a href="{{ route('admin.generator.create') }}" class="btn btn-primary btn-sm"><i data-lucide="plus"></i> {{ __('generator.new') }}</a>
        </div>
        <div class="card-body p-0">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs uppercase text-slate-500 border-b border-slate-200 bg-slate-50">
                        <th class="px-4 py-3 text-left">{{ __('generator.col_model') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('generator.col_table') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('generator.col_connection') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('generator.col_menu') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('generator.col_roles') }}</th>
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

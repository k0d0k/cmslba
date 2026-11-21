@extends('layouts.admin')

@section('title', __('common.settings') . ' - Role & Akses')

@section('content')
    <div class="card">
        <div class="card-header">Role &amp; Hak Akses Menu</div>
        <div class="card-body p-0">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase text-slate-500 border-b border-slate-200">
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Scope</th>
                        <th class="px-4 py-3">Deskripsi</th>
                        <th class="px-4 py-3 text-center">User</th>
                        <th class="px-4 py-3 text-right">{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                        @php $meta = $config[$role->name] ?? null; @endphp
                        <tr class="border-b border-slate-100">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-700">{{ $meta['label'] ?? $role->name }}</div>
                                <code class="text-[11px] text-slate-400">{{ $role->name }}</code>
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge {{ ($meta['scope'] ?? '') === 'principal' ? 'badge-warning' : 'badge-secondary' }}">
                                    {{ $meta['scope'] ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-500">{{ $meta['description'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-center font-semibold">{{ $role->users_count }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.roles.assign', $role->id) }}" class="btn btn-primary btn-sm">{{ __('common.assign') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

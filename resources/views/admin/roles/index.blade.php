@extends('layouts.admin')

@section('title', __('common.settings') . ' - Role & Akses')

@section('content')
    <div class="card">
        <div class="card-header flex items-center justify-between">
            <span>Role &amp; Hak Akses Menu</span>
            <button type="button" class="btn btn-primary btn-sm" data-modal-target="create-role-modal">
                + Tambah Role
            </button>
        </div>
        <div class="card-body p-0 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase text-slate-500 border-b border-slate-200">
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Scope</th>
                        <th class="px-4 py-3">Deskripsi</th>
                        <th class="px-4 py-3 text-center">User</th>
                        <th class="px-4 py-3 text-center">Akses</th>
                        <th class="px-4 py-3 text-right">{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                        @php
                            $meta = $config[$role->name] ?? null;
                            $count = $menuCount[$role->id] ?? 0;
                        @endphp
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
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
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('admin.roles.assign', $role->id) }}"
                                   class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium
                                          {{ $count > 0 ? 'bg-teal-50 text-teal-700 hover:bg-teal-100' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                                    {{ $count }} menu
                                </a>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.roles.assign', $role->id) }}" class="btn btn-primary btn-sm">{{ __('common.assign') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Tambah Role --}}
    <div id="create-role-modal" class="modal-overlay hidden" data-modal>
        <div class="modal-box w-full max-w-md">
            <form method="POST" action="{{ route('admin.roles.store') }}">
                @csrf
                <div class="flex items-center justify-between border-b border-slate-200 pb-3 mb-4">
                    <h3 class="text-lg font-semibold text-slate-800">Tambah Role Baru</h3>
                    <button type="button" class="text-slate-400 hover:text-slate-600" data-modal-close>&times;</button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Role (kode)</label>
                        <input type="text" name="name" class="form-input" placeholder="mis. supervisor_mitra"
                               value="{{ old('name') }}" required>
                        <p class="text-xs text-slate-400 mt-1">Hanya huruf, angka, garis bawah, dan strip.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Label</label>
                        <input type="text" name="label" class="form-input" placeholder="mis. Supervisor Mitra"
                               value="{{ old('label') }}" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Scope</label>
                        <select name="scope" class="form-input" required>
                            <option value="principal">Principal (Pusat)</option>
                            <option value="mitra">Mitra</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                        <textarea name="description" class="form-input" rows="2"
                                  placeholder="Deskripsi singkat role ini...">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="flex items-center gap-2 mt-5 border-t border-slate-200 pt-4">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
                </div>
            </form>
        </div>
    </div>
@endsection

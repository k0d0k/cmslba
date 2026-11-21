@extends('layouts.admin')

@section('title', __('common.settings') . ' - Menu Dinamis')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Struktur menu (drag & drop) --}}
        <div class="lg:col-span-2">
            <div class="card">
                <div class="card-header flex items-center justify-between">
                    <span>Struktur Menu (drag &amp; drop)</span>
                    <span class="text-xs text-slate-400">Ubah posisi/anak dengan menggeser ikon &#8942;&#8942;</span>
                </div>
                <div class="card-body">
                    @if($tree->count())
                        @include('admin.menus.tree', ['items' => $tree, 'level' => 0])
                    @else
                        <p class="text-slate-400 text-sm">{{ __('messages.no_data') }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Form tambah / ubah menu --}}
        <div>
            <div class="card">
                <div class="card-header">{{ $edit ? __('common.edit') : __('common.add') }} Menu</div>
                <form method="POST"
                      action="{{ $edit ? route('admin.menus.update', $edit->key) : route('admin.menus.store') }}"
                      class="card-body space-y-3">
                    @csrf
                    @if($edit)
                        @method('PUT')
                    @endif

                    <div>
                        <label class="form-label">Judul (Indonesia)</label>
                        <input type="text" name="title_id" value="{{ old('title_id', $edit?->title['id'] ?? '') }}" class="form-control" required>
                    </div>
                    <div>
                        <label class="form-label">Judul (English)</label>
                        <input type="text" name="title_en" value="{{ old('title_en', $edit?->title['en'] ?? '') }}" class="form-control">
                    </div>
                    <div>
                        <label class="form-label">Judul (Malaysia)</label>
                        <input type="text" name="title_ms" value="{{ old('title_ms', $edit?->title['ms'] ?? '') }}" class="form-control">
                    </div>

                    <div>
                        <label class="form-label">Parent</label>
                        <select name="parent_id" class="form-control">
                            <option value="">- (root)</option>
                            @foreach($parents as $p)
                                @continue($edit && $p->id === $edit->id)
                                <option value="{{ $p->id }}" @selected((int) old('parent_id', $edit?->parent_id) === $p->id)>{{ $p->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Tipe</label>
                        <select name="type" class="form-control">
                            @foreach(['link' => 'Link', 'group' => 'Group', 'header' => 'Header'] as $val => $lbl)
                                <option value="{{ $val }}" @selected(old('type', $edit?->type ?? 'link') === $val)>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Route Name</label>
                        <input type="text" name="route_name" value="{{ old('route_name', $edit?->route_name) }}" class="form-control" placeholder="admin.pelanggan.index">
                    </div>

                    <div>
                        <label class="form-label">URL (opsional)</label>
                        <input type="text" name="url" value="{{ old('url', $edit?->url) }}" class="form-control" placeholder="https://...">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">Icon</label>
                            <input type="text" name="icon" value="{{ old('icon', $edit?->icon ?? 'circle') }}" class="form-control">
                        </div>
                        <div>
                            <label class="form-label">Urutan</label>
                            <input type="number" name="sort" value="{{ old('sort', $edit?->sort ?? 0) }}" class="form-control">
                        </div>
                    </div>

                    <div class="flex items-center gap-4 text-sm text-slate-600">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_active" value="1" class="form-check" @checked(old('is_active', $edit?->is_active ?? true))>
                            Aktif
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_visible" value="1" class="form-check" @checked(old('is_visible', $edit?->is_visible ?? true))>
                            Tampil
                        </label>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
                        @if($edit)
                            <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary">{{ __('common.cancel') }}</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

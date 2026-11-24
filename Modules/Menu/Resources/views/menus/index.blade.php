@extends('layouts.admin')

@section('title', __('common.settings') . ' - ' . __('menus.group'))

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Struktur menu (drag & drop) --}}
        <div class="lg:col-span-2">
            <div class="card">
                <div class="card-header flex items-center justify-between">
                    <span>{!! __('menus.structure') !!}</span>
                    <span class="text-xs text-slate-400">{!! __('menus.structure_hint') !!}</span>
                </div>
                <div class="card-body">
                    @if($tree->count())
                        @include('menu::menus.tree', ['items' => $tree, 'level' => 0])
                    @else
                        <p class="text-slate-400 text-sm">{{ __('messages.no_data') }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Form tambah / ubah menu --}}
        <div>
            <div class="card">
                <div class="card-header">{{ $edit ? __('common.edit') : __('common.add') }} {{ __('menus.form_title') }}</div>
                <form method="POST"
                      action="{{ $edit ? route('admin.menus.update', $edit->key) : route('admin.menus.store') }}"
                      class="card-body space-y-3">
                    @csrf
                    @if($edit)
                        @method('PUT')
                    @endif

                    <div>
                        <label class="form-label">{{ __('menus.title_id') }}</label>
                        <input type="text" name="title_id" value="{{ old('title_id', $edit?->title['id'] ?? '') }}" class="form-control" required>
                    </div>
                    <div>
                        <label class="form-label">{{ __('menus.title_en') }}</label>
                        <input type="text" name="title_en" value="{{ old('title_en', $edit?->title['en'] ?? '') }}" class="form-control">
                    </div>
                    <div>
                        <label class="form-label">{{ __('menus.title_ms') }}</label>
                        <input type="text" name="title_ms" value="{{ old('title_ms', $edit?->title['ms'] ?? '') }}" class="form-control">
                    </div>

                    <div>
                        <label class="form-label">{{ __('menus.parent') }}</label>
                        <select name="parent_id" class="form-control">
                            <option value="">{{ __('menus.root') }}</option>
                            @foreach($parents as $p)
                                @continue($edit && $p->id === $edit->id)
                                <option value="{{ $p->id }}" @selected((int) old('parent_id', $edit?->parent_id) === $p->id)>{{ $p->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">{{ __('menus.type') }}</label>
                        <select name="type" class="form-control">
                            @foreach(['link' => __('menus.type_link'), 'group' => __('menus.type_group'), 'header' => __('menus.type_header')] as $val => $lbl)
                                <option value="{{ $val }}" @selected(old('type', $edit?->type ?? 'link') === $val)>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">{{ __('menus.module') }}</label>
                        <select name="module_key" class="form-control">
                            <option value="">{{ __('menus.module_none') }}</option>
                            @foreach($modules as $code => $name)
                                <option value="{{ $code }}" @selected(old('module_key', $edit?->module_key) === $code)>{{ $name }}</option>
                            @endforeach
                        </select>
                        <p class="form-hint">{{ __('menus.module_hint') }}</p>
                    </div>

                    <div>
                        <label class="form-label">{{ __('menus.route_name') }}</label>
                        <input type="text" name="route_name" value="{{ old('route_name', $edit?->route_name) }}" class="form-control" placeholder="admin.pelanggan.index">
                    </div>

                    <div>
                        <label class="form-label">{{ __('menus.url_optional') }}</label>
                        <input type="text" name="url" value="{{ old('url', $edit?->url) }}" class="form-control" placeholder="https://...">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">{{ __('menus.icon') }}</label>
                            @include('layouts.partials.icon-picker', ['name' => 'icon', 'value' => $edit?->icon ?? 'circle'])
                        </div>
                        <div>
                            <label class="form-label">{{ __('menus.sort') }}</label>
                            <input type="number" name="sort" value="{{ old('sort', $edit?->sort ?? 0) }}" class="form-control">
                        </div>
                    </div>

                    <div class="flex items-center gap-4 text-sm text-slate-600">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_active" value="1" class="form-check" @checked(old('is_active', $edit?->is_active ?? true))>
                            {{ __('menus.active') }}
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_visible" value="1" class="form-check" @checked(old('is_visible', $edit?->is_visible ?? true))>
                            {{ __('menus.display') }}
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

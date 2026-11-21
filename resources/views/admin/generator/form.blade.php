@extends('layouts.admin')

@section('title', 'CRUD Generator - Buat Baru')

@section('content')
    @php
        $fieldTypes = [
            'string' => 'String', 'text' => 'Textarea', 'email' => 'Email',
            'integer' => 'Integer', 'decimal' => 'Decimal', 'boolean' => 'Boolean',
            'date' => 'Date', 'datetime' => 'DateTime', 'select' => 'Select', 'relation' => 'Relation',
        ];
    @endphp

    <form method="POST" action="{{ route('admin.generator.store') }}" class="space-y-4">
        @csrf

        <div class="card">
            <div class="card-header">Informasi Modul</div>
            <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Judul Menu</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="form-control" required placeholder="Produk">
                </div>
                <div>
                    <label class="form-label">Model</label>
                    <input type="text" name="model" value="{{ old('model') }}" class="form-control" required placeholder="Produk">
                    <p class="text-xs text-slate-400 mt-1">StudlyCase, tanpa spasi. Model: App\Models\Tenant\{Model}</p>
                </div>
                <div>
                    <label class="form-label">Key / Route (opsional)</label>
                    <input type="text" name="key" value="{{ old('key') }}" class="form-control" placeholder="produk">
                </div>
                <div>
                    <label class="form-label">Icon</label>
                    <input type="text" name="icon" value="{{ old('icon', 'table') }}" class="form-control">
                </div>
                <div>
                    <label class="form-label">Parent Menu</label>
                    <select name="parent_id" class="form-control">
                        <option value="">- (root)</option>
                        @foreach($parents as $p)
                            <option value="{{ $p->id }}" @selected(old('parent_id') == $p->id)>{{ $p->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="has_cabang" value="1" class="form-check" @checked(old('has_cabang'))>
                        Sertakan relasi Cabang (multi cabang)
                    </label>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Field</div>
            <div class="card-body">
                <div id="fields" class="space-y-3"></div>
                <button type="button" id="add-field" class="btn btn-secondary btn-sm mt-3">+ Tambah Field</button>
                <p class="text-xs text-slate-400 mt-2">
                    Kolom <b>slug</b> otomatis ditambahkan ke setiap tabel.
                    Untuk tipe <b>Select</b>, isi opsi dengan format <code>key: Label</code> per baris.
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Assign ke Role</div>
            <div class="card-body grid grid-cols-2 md:grid-cols-3 gap-2">
                @foreach($roles as $roleName => $meta)
                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="roles[]" value="{{ $roleName }}" class="form-check"
                               @checked(in_array($roleName, old('roles', [])))>
                        {{ $meta['label'] }}
                    </label>
                @endforeach
                <p class="text-xs text-slate-400 col-span-full">
                    Admin Principal &amp; User Principal otomatis diberi akses penuh.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" class="btn btn-primary">Generate CRUD</button>
            <a href="{{ route('admin.generator.index') }}" class="btn btn-secondary">{{ __('common.cancel') }}</a>
        </div>
    </form>

    <template id="field-template">
        <div class="field-row grid grid-cols-1 md:grid-cols-12 gap-2 items-start border border-slate-200 rounded-lg p-3">
            <div class="md:col-span-3">
                <label class="form-label">Nama Field</label>
                <input type="text" name="field_name[]" class="form-control" placeholder="name">
            </div>
            <div class="md:col-span-2">
                <label class="form-label">Tipe</label>
                <select name="field_type[]" class="form-control">
                    @foreach($fieldTypes as $val => $lbl)
                        <option value="{{ $val }}">{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-3">
                <label class="form-label">Label</label>
                <input type="text" name="field_label[]" class="form-control" placeholder="Nama">
            </div>
            <div class="md:col-span-2 flex items-center gap-3 pt-6 text-xs text-slate-600">
                <label class="flex items-center gap-1">
                    <input type="checkbox" name="field_nullable[]" value="1" class="form-check"> Nullable
                </label>
                <label class="flex items-center gap-1">
                    <input type="checkbox" name="field_unique[]" value="1" class="form-check"> Unik
                </label>
            </div>
            <div class="md:col-span-2 pt-6">
                <button type="button" class="btn btn-danger btn-sm remove-field w-full justify-center">Hapus</button>
            </div>
            <div class="md:col-span-12">
                <label class="form-label">Opsi (khusus Select) — format <code>key: Label</code> per baris</label>
                <textarea name="field_options[]" rows="2" class="form-control" placeholder="active: Aktif&#10;inactive: Nonaktif"></textarea>
            </div>
        </div>
    </template>

    <script>
        (function () {
            const wrap = document.getElementById('fields');
            const tpl = document.getElementById('field-template');

            function addRow() {
                wrap.appendChild(tpl.content.cloneNode(true));
            }

            document.getElementById('add-field').addEventListener('click', addRow);
            wrap.addEventListener('click', (e) => {
                if (e.target.closest('.remove-field')) {
                    e.target.closest('.field-row').remove();
                }
            });

            // Minimal satu field
            addRow();
            addRow();
        })();
    </script>
@endsection

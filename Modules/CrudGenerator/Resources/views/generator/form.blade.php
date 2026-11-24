@extends('layouts.admin')

@section('title', __('generator.title_new'))

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
            <div class="card-header">{{ __('generator.module_info') }}</div>
            <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">{{ __('generator.menu_title') }}</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="form-control" required placeholder="{{ __('generator.menu_placeholder') }}">
                </div>
                <div>
                    <label class="form-label">{{ __('generator.model') }}</label>
                    <input type="text" name="model" value="{{ old('model') }}" class="form-control" required placeholder="{{ __('generator.menu_placeholder') }}">
                    <p class="text-xs text-slate-400 mt-1">{{ __('generator.model_hint') }}</p>
                </div>
                <div>
                    <label class="form-label">{{ __('generator.key_route') }}</label>
                    <input type="text" name="key" value="{{ old('key') }}" class="form-control" placeholder="produk">
                </div>
                <div>
                    <label class="form-label">{{ __('common.icon') }}</label>
                    @include('layouts.partials.icon-picker', ['name' => 'icon', 'value' => 'table'])
                </div>
                <div>
                    <label class="form-label">{{ __('generator.database') }}</label>
                    <select name="database_target" id="database_target" class="form-control" onchange="document.getElementById('connection').value = this.value">
                        <option value="tenant" @selected(old('connection', 'tenant') === 'tenant')>{{ __('generator.db_tenant') }}</option>
                        <option value="central" @selected(old('connection') === 'central')>{{ __('generator.db_central') }}</option>
                    </select>
                    <input type="hidden" name="connection" id="connection" value="{{ old('connection', 'tenant') }}">
                    <p class="text-xs text-slate-400 mt-1">
                        {!! __('generator.db_hint') !!}
                    </p>
                </div>
                <div>
                    <label class="form-label">{{ __('generator.parent_menu') }}</label>
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
                        {{ __('generator.include_cabang') }}
                    </label>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">{{ __('generator.field') }}</div>
            <div class="card-body">
                <div id="fields" class="space-y-3"></div>
                <button type="button" id="add-field" class="btn btn-secondary btn-sm mt-3">{{ __('generator.add_field') }}</button>
                <p class="text-xs text-slate-400 mt-2">
                    {!! __('generator.field_note') !!}
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">{{ __('generator.assign_role') }}</div>
            <div class="card-body grid grid-cols-2 md:grid-cols-3 gap-2">
                @foreach($roles as $roleName => $meta)
                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="roles[]" value="{{ $roleName }}" class="form-check"
                               @checked(in_array($roleName, old('roles', [])))>
                        {{ $meta['label'] }}
                    </label>
                @endforeach
                <p class="text-xs text-slate-400 col-span-full">
                    {!! __('generator.assign_hint') !!}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" class="btn btn-primary">{{ __('generator.generate') }}</button>
            <a href="{{ route('admin.generator.index') }}" class="btn btn-secondary">{{ __('common.cancel') }}</a>
        </div>
    </form>

    <template id="field-template">
        <div class="field-row grid grid-cols-1 md:grid-cols-12 gap-2 items-start border border-slate-200 rounded-lg p-3">
            <div class="md:col-span-3">
                <label class="form-label">{{ __('generator.field_name') }}</label>
                <input type="text" name="field_name[]" class="form-control" placeholder="{{ __('generator.field_name_placeholder') }}">
            </div>
            <div class="md:col-span-2">
                <label class="form-label">{{ __('generator.field_type') }}</label>
                <select name="field_type[]" class="form-control">
                    @foreach($fieldTypes as $val => $lbl)
                        <option value="{{ $val }}">{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-3">
                <label class="form-label">{{ __('generator.field_label') }}</label>
                <input type="text" name="field_label[]" class="form-control" placeholder="{{ __('generator.field_label_placeholder') }}">
            </div>
            <div class="md:col-span-2 flex items-center gap-3 pt-6 text-xs text-slate-600">
                <label class="flex items-center gap-1">
                    <input type="checkbox" name="field_nullable[]" value="1" class="form-check"> {{ __('generator.nullable') }}
                </label>
                <label class="flex items-center gap-1">
                    <input type="checkbox" name="field_unique[]" value="1" class="form-check"> {{ __('generator.unique') }}
                </label>
            </div>
            <div class="md:col-span-2 pt-6">
                <button type="button" class="btn btn-danger btn-sm remove-field w-full justify-center">{{ __('generator.remove') }}</button>
            </div>
            <div class="md:col-span-12">
                <label class="form-label">{{ __('generator.options') }} — format <code>key: Label</code> {{ __('generator.per_line') }}</label>
                <textarea name="field_options[]" rows="2" class="form-control" placeholder="{{ __('common.active') }}: {{ __('common.active') }}&#10;{{ __('common.inactive') }}: {{ __('common.inactive') }}"></textarea>
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

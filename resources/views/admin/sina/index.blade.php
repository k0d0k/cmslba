@extends('layouts.admin')

@section('title', __('sina.title'))

@section('content')
    @if(! $mitra)
        <div class="card">
            <div class="card-body text-sm text-slate-500">
                {{ __('sina.no_mitra') }}
            </div>
        </div>
    @else
        @php
            $usageColumns = [
                ['data' => 'recorded_at',        'name' => 'recorded_at'],
                ['data' => 'job_reference',      'name' => 'job_reference'],
                ['data' => 'provider',           'name' => 'provider'],
                ['data' => 'model_name',         'name' => 'model_name'],
                ['data' => 'unit_price_credits', 'name' => 'unit_price_credits'],
                ['data' => 'credit_units',       'name' => 'credit_units'],
                ['data' => 'total_credits',      'name' => 'total_credits', 'orderable' => false, 'searchable' => false],
            ];
            $topupColumns = [
                ['data' => 'created_at',       'name' => 'created_at'],
                ['data' => 'credits',          'name' => 'credits'],
                ['data' => 'price_per_credit', 'name' => 'price_per_credit'],
                ['data' => 'total_amount',     'name' => 'total_amount'],
                ['data' => 'note',             'name' => 'note'],
            ];
        @endphp

        <div class="space-y-4">

            {{-- ===================== Header + Pilihan Mitra ===================== --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h1 class="text-lg font-semibold text-slate-700">{{ __('sina.title') }}</h1>
                    <p class="text-xs text-slate-500">{{ __('sina.subtitle') }}</p>
                </div>

                @if($isPrincipal)
                    {{-- Select2 untuk memilih mitra (bisa dicari); navigasi ke ?mitra=slug --}}
                    <form id="mitra-picker" method="GET" action="{{ route('admin.sina.index') }}" data-submit-on-change="1" class="flex items-center gap-2" style="min-width:320px">
                        <label class="text-xs text-slate-500 whitespace-nowrap" for="mitra-select">{{ __('sina.mitra') }}</label>
                        <select id="mitra-select" name="mitra" class="js-select2" data-placeholder="{{ __('sina.search_mitra') }}" data-allow-clear="0" style="width:100%">
                            @foreach($mitras as $m)
                                <option value="{{ $m->slug }}" @selected($mitra->id === $m->id)>{{ $m->name }}</option>
                            @endforeach
                        </select>
                        <noscript>
                            <button type="submit" class="btn btn-primary btn-sm">{{ __('sina.open') }}</button>
                        </noscript>
                    </form>
                @else
                    <span class="badge badge-secondary">{{ $mitra->name }}</span>
                @endif
            </div>

            {{-- Mitra yang sedang aktif (ditampilkan di semua tab) --}}
            <div class="text-xs text-slate-500">
                {{ __('sina.active_mitra') }}: <span class="font-medium text-slate-700">{{ $mitra->name }}</span>
                @if($isPrincipal) <span class="text-slate-400">({{ $mitra->slug }})</span> @endif
            </div>

            {{-- ===================== Tab ===================== --}}
            <div class="card">
                <div class="border-b border-slate-200 px-3 pt-2">
                    <nav id="sina-tabs" class="flex flex-wrap">
                        <button type="button" class="sina-tab" data-tab="endpoint">{{ __('sina.tab_endpoint') }}</button>
                        <button type="button" class="sina-tab" data-tab="price">{{ __('sina.tab_price') }}</button>
                        <button type="button" class="sina-tab" data-tab="balance">{{ __('sina.tab_balance') }}</button>
                        <button type="button" class="sina-tab" data-tab="topup">{{ __('sina.tab_topup') }}</button>
                    </nav>
                </div>

                <div class="card-body">

                    {{-- ---------- TAB: EndPoin Penyedia SINA ---------- --}}
                    <div class="sina-panel" data-panel="endpoint">
                        @if($canUpdate)
                            <form method="POST" action="{{ route('admin.sina.config') }}">
                                @csrf
                                <input type="hidden" name="mitra_id" value="{{ $mitra->id }}">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="form-label">{{ __('sina.provider') }}</label>
                                        <select name="provider" class="form-control">
                                            @foreach($providers as $value => $label)
                                                <option value="{{ $value }}" @selected(old('provider', $config->provider ?? 'ollama') === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form-label">{{ __('sina.label') }}</label>
                                        <input type="text" name="label" value="{{ old('label', $config->label ?? '') }}"
                                               class="form-control" placeholder="{{ __('sina.label_placeholder') }}">
                                    </div>
                                    <div>
                                        <label class="form-label">{{ __('sina.base_url') }}</label>
                                        <input type="text" name="base_url" value="{{ old('base_url', $config->base_url ?? '') }}"
                                               class="form-control" placeholder="http://127.0.0.1:3000" required>
                                        @error('base_url')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="form-label">{{ __('sina.model') }}</label>
                                        <input type="text" name="model" value="{{ old('model', $config->model ?? 'Sina-latest') }}"
                                               class="form-control" placeholder="Sina-latest" required>
                                        @error('model')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="form-label">{{ __('sina.api_key') }}</label>
                                        <input type="password" name="api_key" value="{{ old('api_key', $config->api_key ?? '') }}"
                                               class="form-control" placeholder="{{ __('sina.api_key_placeholder') }}">
                                    </div>
                                    <div class="flex items-center gap-6 md:col-span-2">
                                        <label class="flex items-center gap-2 text-sm text-slate-600">
                                            <input type="checkbox" name="enabled" value="1" class="form-check"
                                                   @checked(old('enabled', $config->enabled ?? true))>
                                            {{ __('sina.enable_integration') }}
                                        </label>
                                        <label class="flex items-center gap-2 text-sm text-slate-600">
                                            <input type="checkbox" name="is_default" value="1" class="form-check"
                                                   @checked(old('is_default', $config->is_default ?? true))>
                                            {{ __('sina.make_default') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="pt-4">
                                    <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
                                </div>
                            </form>
                        @else
                            <p class="text-sm text-slate-500">{{ __('sina.no_permission_config') }}</p>
                        @endif
                    </div>

                    {{-- ---------- TAB: Token & Harga ---------- --}}
                    <div class="sina-panel hidden" data-panel="price">
                        <p class="text-xs text-slate-500 mb-4">
                            {{ __('sina.price_hint') }}
                        </p>

                        @if($canManagePrice)
                            <form method="POST" action="{{ route('admin.sina.pricing') }}" class="space-y-4">
                                @csrf
                                <label class="flex items-center gap-2 text-sm text-slate-700">
                                    <input type="checkbox" name="billing_enabled" value="1" class="form-check"
                                           @checked(old('billing_enabled', $pricing['billing_enabled']))>
                                    {{ __('sina.billing_enabled') }}
                                </label>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="form-label">{{ __('sina.credit_per_chat') }}</label>
                                        <input type="number" min="1" name="sina_credit_per_chat"
                                               value="{{ old('sina_credit_per_chat', $pricing['credit_per_chat']) }}"
                                               class="form-control" required>
                                        @error('sina_credit_per_chat')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="form-label">{{ __('sina.credit_per_1000') }}</label>
                                        <input type="number" min="1" name="sina_credit_per_1000_tokens"
                                               value="{{ old('sina_credit_per_1000_tokens', $pricing['credit_per_1000']) }}"
                                               class="form-control" required>
                                        @error('sina_credit_per_1000_tokens')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="form-label">{{ __('sina.unit') }}</label>
                                        <input type="text" name="sina_unit"
                                               value="{{ old('sina_unit', $pricing['unit']) }}"
                                               class="form-control" required>
                                        @error('sina_unit')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                    </div>
                                </div>

                                <p class="text-xs text-slate-500">
                                    {{ __('sina.price_value_hint') }}
                                </p>

                                <button type="submit" class="btn btn-primary">{{ __('sina.save_price') }}</button>
                            </form>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 max-w-2xl">
                                <div class="rounded-lg border border-slate-200 p-3">
                                    <div class="text-xs text-slate-500">{{ __('sina.credit_per_chat') }}</div>
                                    <div class="text-lg font-semibold text-slate-700">{{ $pricing['credit_per_chat'] }}</div>
                                </div>
                                <div class="rounded-lg border border-slate-200 p-3">
                                    <div class="text-xs text-slate-500">{{ __('sina.credit_per_1000') }}</div>
                                    <div class="text-lg font-semibold text-slate-700">{{ $pricing['credit_per_1000'] }}</div>
                                </div>
                                <div class="rounded-lg border border-slate-200 p-3">
                                    <div class="text-xs text-slate-500">{{ __('sina.unit') }}</div>
                                    <div class="text-lg font-semibold text-slate-700">{{ $pricing['unit'] }}</div>
                                </div>
                            </div>
                            <p class="text-sm text-slate-500 mt-3">{{ __('sina.price_principal_only') }}</p>
                        @endif
                    </div>

                    {{-- ---------- TAB: Saldo Kredit SINA AI ---------- --}}
                    <div class="sina-panel hidden" data-panel="balance">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-5">
                            <div class="lg:col-span-1 rounded-lg border border-slate-200 p-4">
                                <div class="text-xs text-slate-500">{{ __('sina.balance_available') }}</div>
                                <div class="flex items-end gap-2 mt-1">
                                    <span class="text-3xl font-bold text-slate-800">{{ number_format($wallet->balance_credits) }}</span>
                                    <span class="text-sm text-slate-500 mb-1">{{ $pricing['unit'] }}</span>
                                </div>
                                <div class="text-xs text-slate-400 mt-2">{{ __('sina.mitra') }}: {{ $mitra->name }}</div>
                            </div>
                            <div class="lg:col-span-2 grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <div class="rounded-lg border border-slate-200 p-3">
                                    <div class="text-xs text-slate-500">{{ __('sina.granted_balance') }}</div>
                                    <div class="text-lg font-semibold text-slate-700">{{ number_format($wallet->granted_credits) }}</div>
                                </div>
                                <div class="rounded-lg border border-slate-200 p-3">
                                    <div class="text-xs text-slate-500">{{ __('sina.total_topup') }}</div>
                                    <div class="text-lg font-semibold text-slate-700">{{ number_format($wallet->topped_up_credits) }}</div>
                                </div>
                                <div class="rounded-lg border border-slate-200 p-3">
                                    <div class="text-xs text-slate-500">{{ __('sina.used') }}</div>
                                    <div class="text-lg font-semibold text-slate-700">{{ number_format($wallet->used_credits) }}</div>
                                </div>
                                <div class="rounded-lg border border-slate-200 p-3">
                                    <div class="text-xs text-slate-500">{{ __('sina.period') }}</div>
                                    <div class="text-sm font-medium text-slate-700 mt-1">
                                        @if($wallet->period_end)
                                            {{ optional($wallet->period_start)->format('d M Y') }} &ndash; {{ $wallet->period_end->format('d M Y') }}
                                        @else
                                            &mdash;
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p class="text-xs text-slate-400 mb-3">
                            {{ __('sina.balance_hint', ['chat' => $pricing['credit_per_chat'], 'unit' => $pricing['unit']]) }}
                        </p>

                        <h3 class="text-sm font-semibold text-slate-600 mb-2">{{ __('sina.usage_history') }}</h3>
                        <div class="border border-slate-200 rounded-lg overflow-hidden">
                            <table class="js-datatable w-full display"
                                   data-dt-url="{{ route('admin.sina.data', ['mitra' => $mitra->slug]) }}"
                                   data-dt-columns='@json($usageColumns)'
                                   data-dt-actions="0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('sina.th_time') }}</th>
                                        <th>{{ __('sina.th_reference') }}</th>
                                        <th>{{ __('sina.th_provider') }}</th>
                                        <th>{{ __('sina.th_model') }}</th>
                                        <th>{{ __('sina.th_unit_price') }}</th>
                                        <th>{{ __('sina.th_unit') }}</th>
                                        <th>{{ __('sina.th_total_credits') }}</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    {{-- ---------- TAB: Tambah Kredit (Top-up) ---------- --}}
                    <div class="sina-panel hidden" data-panel="topup">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
                            <div class="lg:col-span-2">
                                @if($canUpdate)
                                    <form method="POST" action="{{ route('admin.sina.topup') }}" class="space-y-3">
                                        @csrf
                                        <input type="hidden" name="mitra_id" value="{{ $mitra->id }}">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="form-label">{{ __('sina.mitra') }}</label>
                                                <input type="text" value="{{ $mitra->name }}" class="form-control bg-slate-50" disabled>
                                                <p class="text-xs text-slate-400 mt-1">{{ __('sina.mitra_change_hint') }}</p>
                                            </div>
                                            <div>
                                                <label class="form-label">{{ __('sina.credit_amount', ['unit' => $pricing['unit']]) }}</label>
                                                <input type="number" name="credits" min="1"
                                                       value="{{ old('credits') }}" class="form-control" placeholder="{{ __('sina.credits_placeholder') }}" required>
                                                @error('credits')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="form-label">{{ __('sina.note') }}</label>
                                                <input type="text" name="note" value="{{ old('note') }}" class="form-control"
                                                       placeholder="{{ __('sina.note_placeholder') }}">
                                            </div>
                                        </div>

                                        <p class="text-xs text-slate-500">
                                            {{ __('sina.topup_hint', ['chat' => $pricing['credit_per_chat'], 'unit' => $pricing['unit']]) }}
                                        </p>

                                        <button type="submit" class="btn btn-primary">
                                            <i data-lucide="plus"></i> {{ __('sina.save_topup') }}
                                        </button>
                                    </form>
                                @else
                                    <p class="text-sm text-slate-500">{{ __('sina.no_permission_topup') }}</p>
                                @endif
                            </div>
                            <div class="rounded-lg border border-slate-200 p-4">
                                <div class="text-xs text-slate-500">{{ __('sina.balance_available') }}</div>
                                <div class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($wallet->balance_credits) }}
                                    <span class="text-sm font-normal text-slate-500">{{ $pricing['unit'] }}</span>
                                </div>
                            </div>
                        </div>

                        <h3 class="text-sm font-semibold text-slate-600 mb-2">{{ __('sina.topup_history') }}</h3>
                        <div class="border border-slate-200 rounded-lg overflow-hidden">
                            <table class="js-datatable w-full display"
                                   data-dt-url="{{ route('admin.sina.topups', ['mitra' => $mitra->slug]) }}"
                                   data-dt-columns='@json($topupColumns)'
                                   data-dt-actions="0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('sina.th_time') }}</th>
                                        <th>{{ __('sina.th_credit') }}</th>
                                        <th>{{ __('sina.th_credit_chat') }}</th>
                                        <th>{{ __('sina.th_total_credits') }}</th>
                                        <th>{{ __('sina.th_note') }}</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <style>
            .sina-tab { padding: .55rem .9rem; font-size: .8125rem; font-weight: 600; color: #64748b; border-bottom: 2px solid transparent; cursor: pointer; }
            .sina-tab:hover { color: #334155; }
            .sina-tab.active { color: var(--color-primary); border-bottom-color: var(--color-primary); }
        </style>

        <script>
            (function () {
                // ----- Tab -----
                var tabs = Array.prototype.slice.call(document.querySelectorAll('.sina-tab'));
                var panels = Array.prototype.slice.call(document.querySelectorAll('.sina-panel'));
                function show(key) {
                    tabs.forEach(function (t) { t.classList.toggle('active', t.dataset.tab === key); });
                    panels.forEach(function (p) { p.classList.toggle('hidden', p.dataset.panel !== key); });
                    try { sessionStorage.setItem('sina_tab', key); } catch (e) {}
                }
                tabs.forEach(function (t) { t.addEventListener('click', function () { show(t.dataset.tab); }); });
                var initial = 'endpoint';
                try {
                    var saved = sessionStorage.getItem('sina_tab');
                    if (saved && tabs.some(function (t) { return t.dataset.tab === saved; })) { initial = saved; }
                } catch (e) {}
                show(initial);

                // ----- Mitra picker (Select2): navigasi saat pilihan berubah -----
                // Ditangani oleh initSelect2() di app.js: form dengan
                // data-submit-on-change="1" akan di-submit otomatis (method GET)
                // sehingga URL menjadi ?mitra=slug_mitra. Native change di sini
                // hanya sebagai fallback bila app.js belum termuat.
                var picker = document.getElementById('mitra-picker');
                var selEl = document.getElementById('mitra-select');
                if (picker && selEl && !picker.dataset.submitOnChangeHandled) {
                    picker.dataset.submitOnChangeHandled = '1';
                    selEl.addEventListener('change', function () {
                        picker.requestSubmit();
                    });
                }
            })();
        </script>
    @endif
@endsection

@extends('layouts.admin')

@section('title', 'Integrasi SINA AI')

@section('content')
    @if(! $mitra)
        <div class="card">
            <div class="card-body text-sm text-slate-500">
                Belum ada mitra yang dapat dikelola. Tambahkan mitra terlebih dahulu.
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
                    <h1 class="text-lg font-semibold text-slate-700">Integrasi SINA AI</h1>
                    <p class="text-xs text-slate-500">Kelola EndPoin penyedia, harga token, saldo kredit, dan top-up.</p>
                </div>

                @if($isPrincipal)
                    <form method="GET" action="{{ route('admin.sina.index') }}" class="flex items-center gap-2">
                        <label class="text-xs text-slate-500">Mitra</label>
                        <select name="mitra" onchange="this.form.submit()" class="form-control">
                            @foreach($mitras as $m)
                                <option value="{{ $m->id }}" @selected($mitra->id === $m->id)>{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </form>
                @else
                    <span class="badge badge-secondary">{{ $mitra->name }}</span>
                @endif
            </div>

            {{-- ===================== Tab ===================== --}}
            <div class="card">
                <div class="border-b border-slate-200 px-3 pt-2">
                    <nav id="sina-tabs" class="flex flex-wrap">
                        <button type="button" class="sina-tab" data-tab="endpoint">EndPoin Penyedia SINA</button>
                        <button type="button" class="sina-tab" data-tab="price">Token &amp; Harga</button>
                        <button type="button" class="sina-tab" data-tab="balance">Saldo Kredit SINA AI</button>
                        <button type="button" class="sina-tab" data-tab="topup">Tambah Kredit (Top-up)</button>
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
                                        <label class="form-label">Penyedia</label>
                                        <select name="provider" class="form-control">
                                            @foreach($providers as $value => $label)
                                                <option value="{{ $value }}" @selected(old('provider', $config->provider ?? 'ollama') === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form-label">Label</label>
                                        <input type="text" name="label" value="{{ old('label', $config->label ?? '') }}"
                                               class="form-control" placeholder="Nama tampilan (opsional)">
                                    </div>
                                    <div>
                                        <label class="form-label">Base URL</label>
                                        <input type="text" name="base_url" value="{{ old('base_url', $config->base_url ?? '') }}"
                                               class="form-control" placeholder="http://127.0.0.1:3000" required>
                                        @error('base_url')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="form-label">Model</label>
                                        <input type="text" name="model" value="{{ old('model', $config->model ?? 'Sina-latest') }}"
                                               class="form-control" placeholder="Sina-latest" required>
                                        @error('model')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="form-label">API Key</label>
                                        <input type="password" name="api_key" value="{{ old('api_key', $config->api_key ?? '') }}"
                                               class="form-control" placeholder="Biarkan kosong bila tidak diperlukan">
                                    </div>
                                    <div class="flex items-center gap-6 md:col-span-2">
                                        <label class="flex items-center gap-2 text-sm text-slate-600">
                                            <input type="checkbox" name="enabled" value="1" class="form-check"
                                                   @checked(old('enabled', $config->enabled ?? true))>
                                            Aktifkan integrasi
                                        </label>
                                        <label class="flex items-center gap-2 text-sm text-slate-600">
                                            <input type="checkbox" name="is_default" value="1" class="form-check"
                                                   @checked(old('is_default', $config->is_default ?? true))>
                                            Jadikan penyedia utama
                                        </label>
                                    </div>
                                </div>
                                <div class="pt-4">
                                    <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
                                </div>
                            </form>
                        @else
                            <p class="text-sm text-slate-500">Anda tidak memiliki hak untuk mengubah konfigurasi.</p>
                        @endif
                    </div>

                    {{-- ---------- TAB: Token & Harga ---------- --}}
                    <div class="sina-panel hidden" data-panel="price">
                        <div class="max-w-xl">
                            <div class="rounded-lg border border-slate-200 p-4 mb-4">
                                <div class="text-xs text-slate-500">Harga token saat ini</div>
                                <div class="text-2xl font-bold text-slate-800 mt-1">
                                    Rp {{ number_format($tokenPrice, 0, ',', '.') }}
                                    <span class="text-sm font-normal text-slate-500">/ kredit</span>
                                </div>
                            </div>

                            @if($canManagePrice)
                                <form method="POST" action="{{ route('admin.sina.token-price') }}" class="space-y-3">
                                    @csrf
                                    <div>
                                        <label class="form-label">Harga per Kredit (Rp)</label>
                                        <input type="number" step="0.01" min="0" name="token_price_per_credit"
                                               value="{{ old('token_price_per_credit', $tokenPrice) }}"
                                               class="form-control" placeholder="contoh: 150" required>
                                        @error('token_price_per_credit')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                    </div>
                                    <p class="text-xs text-slate-400">
                                        Harga berlaku global untuk semua mitra dan menjadi dasar perhitungan biaya top-up.
                                    </p>
                                    <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
                                </form>
                            @else
                                <p class="text-sm text-slate-500">Hanya principal yang dapat mengubah harga token.</p>
                            @endif
                        </div>
                    </div>

                    {{-- ---------- TAB: Saldo Kredit SINA AI ---------- --}}
                    <div class="sina-panel hidden" data-panel="balance">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-5">
                            <div class="lg:col-span-1 rounded-lg border border-slate-200 p-4">
                                <div class="text-xs text-slate-500">Saldo tersedia</div>
                                <div class="flex items-end gap-2 mt-1">
                                    <span class="text-3xl font-bold text-slate-800">{{ number_format($wallet->balance_credits) }}</span>
                                    <span class="text-sm text-slate-500 mb-1">kredit</span>
                                </div>
                                <div class="text-xs text-slate-400 mt-2">Mitra: {{ $mitra->name }}</div>
                            </div>
                            <div class="lg:col-span-2 grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <div class="rounded-lg border border-slate-200 p-3">
                                    <div class="text-xs text-slate-500">Saldo Awal Paket</div>
                                    <div class="text-lg font-semibold text-slate-700">{{ number_format($wallet->granted_credits) }}</div>
                                </div>
                                <div class="rounded-lg border border-slate-200 p-3">
                                    <div class="text-xs text-slate-500">Total Top-up</div>
                                    <div class="text-lg font-semibold text-slate-700">{{ number_format($wallet->topped_up_credits) }}</div>
                                </div>
                                <div class="rounded-lg border border-slate-200 p-3">
                                    <div class="text-xs text-slate-500">Terpakai</div>
                                    <div class="text-lg font-semibold text-slate-700">{{ number_format($wallet->used_credits) }}</div>
                                </div>
                                <div class="rounded-lg border border-slate-200 p-3">
                                    <div class="text-xs text-slate-500">Periode</div>
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
                            Saldo awal berasal dari paket langganan: Kredit SINA AI per pasien per periode &times; jumlah kursi.
                        </p>

                        <h3 class="text-sm font-semibold text-slate-600 mb-2">Riwayat Pemakaian Kredit</h3>
                        <div class="border border-slate-200 rounded-lg overflow-hidden">
                            <table class="js-datatable w-full display"
                                   data-dt-url="{{ route('admin.sina.data', ['mitra' => $mitra->id]) }}"
                                   data-dt-columns='@json($usageColumns)'
                                   data-dt-actions="0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Waktu</th>
                                        <th>Referensi</th>
                                        <th>Penyedia</th>
                                        <th>Model</th>
                                        <th>Harga Satuan</th>
                                        <th>Unit</th>
                                        <th>Total Kredit</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    {{-- ---------- TAB: Tambah Kredit (Top-up) ---------- --}}
                    <div class="sina-panel hidden" data-panel="topup">
                        @if($isPrincipal)
                            <form method="GET" action="{{ route('admin.sina.index') }}" class="mb-4 flex items-center gap-2">
                                <label class="text-xs text-slate-500">Mitra</label>
                                <select name="mitra" onchange="this.form.submit()" class="form-control" style="max-width:280px">
                                    @foreach($mitras as $m)
                                        <option value="{{ $m->id }}" @selected($mitra->id === $m->id)>{{ $m->name }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @endif

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
                            <div class="lg:col-span-2">
                                @if($canUpdate)
                                    <form id="topup-form" method="POST" action="{{ route('admin.sina.topup') }}"
                                          data-price="{{ $tokenPrice }}" class="space-y-3">
                                        @csrf
                                        <input type="hidden" name="mitra_id" value="{{ $mitra->id }}">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="form-label">Jumlah Kredit</label>
                                                <input id="topup-credits" type="number" name="credits" min="1"
                                                       value="{{ old('credits') }}" class="form-control" placeholder="contoh: 1000" required>
                                                @error('credits')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                            </div>
                                            <div>
                                                <label class="form-label">Catatan</label>
                                                <input type="text" name="note" value="{{ old('note') }}" class="form-control"
                                                       placeholder="Catatan top-up (opsional)">
                                            </div>
                                        </div>

                                        <div class="rounded-lg bg-slate-50 border border-slate-200 p-3 text-sm text-slate-600">
                                            Harga per kredit: Rp {{ number_format($tokenPrice, 0, ',', '.') }} &nbsp;&middot;&nbsp;
                                            Estimasi biaya: <span id="topup-total" class="font-semibold text-slate-800">Rp 0</span>
                                        </div>

                                        <button type="submit" class="btn btn-primary">
                                            <i data-lucide="plus"></i> Simpan Top-up
                                        </button>
                                    </form>
                                @else
                                    <p class="text-sm text-slate-500">Anda tidak memiliki hak untuk menambah kredit.</p>
                                @endif
                            </div>
                            <div class="rounded-lg border border-slate-200 p-4">
                                <div class="text-xs text-slate-500">Saldo tersedia</div>
                                <div class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($wallet->balance_credits) }} <span class="text-sm font-normal text-slate-500">kredit</span></div>
                            </div>
                        </div>

                        <h3 class="text-sm font-semibold text-slate-600 mb-2">Riwayat Top-up Kredit</h3>
                        <div class="border border-slate-200 rounded-lg overflow-hidden">
                            <table class="js-datatable w-full display"
                                   data-dt-url="{{ route('admin.sina.topups', ['mitra' => $mitra->id]) }}"
                                   data-dt-columns='@json($topupColumns)'
                                   data-dt-actions="0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Waktu</th>
                                        <th>Kredit</th>
                                        <th>Harga/Kredit</th>
                                        <th>Total Biaya</th>
                                        <th>Catatan</th>
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

                // Kalkulator biaya top-up (kredit x harga per kredit).
                var form = document.getElementById('topup-form');
                if (form) {
                    var price = parseFloat(form.dataset.price || '0');
                    var input = document.getElementById('topup-credits');
                    var total = document.getElementById('topup-total');
                    var calc = function () {
                        var c = parseFloat(input.value || '0') || 0;
                        total.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(c * price));
                    };
                    input.addEventListener('input', calc);
                    calc();
                }
            })();
        </script>
    @endif
@endsection

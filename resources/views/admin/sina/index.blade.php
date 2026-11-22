@extends('layouts.admin')

@section('title', 'Integrasi SINA AI')

@section('content')
    @if(! $mitra)
        <div class="card">
            <div class="card-body text-sm text-slate-500">
                Tidak ada mitra aktif. Pilih mitra terlebih dahulu pada pemilih mitra di bagian atas.
            </div>
        </div>
    @else
        @php
            $columns = [
                ['data' => 'recorded_at',        'name' => 'recorded_at'],
                ['data' => 'job_reference',      'name' => 'job_reference'],
                ['data' => 'provider',           'name' => 'provider'],
                ['data' => 'model_name',         'name' => 'model_name'],
                ['data' => 'unit_price_credits', 'name' => 'unit_price_credits'],
                ['data' => 'credit_units',       'name' => 'credit_units'],
                ['data' => 'total_credits',      'name' => 'total_credits', 'orderable' => false, 'searchable' => false],
            ];
        @endphp

        <div class="space-y-4">

            {{-- ===================== Saldo + Top-up ===================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="card lg:col-span-2">
                    <div class="card-header flex items-center justify-between">
                        <span>Saldo Kredit SINA AI</span>
                        <span class="badge badge-secondary">{{ $mitra->name }}</span>
                    </div>
                    <div class="card-body">
                        <div class="flex items-end gap-2">
                            <span class="text-3xl font-bold text-slate-800">{{ number_format($wallet->balance_credits) }}</span>
                            <span class="text-sm text-slate-500 mb-1">kredit tersedia</span>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-5">
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
                                <div class="text-xs text-slate-500">Periode Berlangganan</div>
                                <div class="text-sm font-medium text-slate-700 mt-1">
                                    @if($wallet->period_end)
                                        {{ optional($wallet->period_start)->format('d M Y') }} &ndash; {{ $wallet->period_end->format('d M Y') }}
                                    @else
                                        &mdash;
                                    @endif
                                </div>
                            </div>
                        </div>

                        <p class="text-xs text-slate-400 mt-4">
                            Saldo awal berasal dari paket langganan: Kredit SINA AI per pasien per periode &times; jumlah kursi.
                            Kredit dapat ditambah kapan saja sebelum masa berlangganan berakhir.
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Tambah Kredit (Top-up)</div>
                    <div class="card-body">
                        @if($canUpdate)
                            <form method="POST" action="{{ route('admin.sina.topup') }}" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="form-label">Jumlah Kredit</label>
                                    <input type="number" name="credits" min="1" value="{{ old('credits') }}"
                                           class="form-control" placeholder="contoh: 1000" required>
                                    @error('credits')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary w-full justify-center">
                                    <i data-lucide="plus"></i> Tambah Kredit
                                </button>
                            </form>
                        @else
                            <p class="text-sm text-slate-500">Anda tidak memiliki hak untuk menambah kredit.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ===================== Konfigurasi Provider ===================== --}}
            <div class="card">
                <div class="card-header">Konfigurasi Penyedia AI</div>
                <div class="card-body">
                    @if($canUpdate)
                        <form method="POST" action="{{ route('admin.sina.config') }}">
                            @csrf
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
                                    @error('base_url')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="form-label">Model</label>
                                    <input type="text" name="model" value="{{ old('model', $config->model ?? 'Sina-latest') }}"
                                           class="form-control" placeholder="Sina-latest" required>
                                    @error('model')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
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

                            <div class="flex items-center gap-2 pt-4">
                                <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
                            </div>
                        </form>
                    @else
                        <p class="text-sm text-slate-500">Anda tidak memiliki hak untuk mengubah konfigurasi.</p>
                    @endif
                </div>
            </div>

            {{-- ===================== Riwayat Pemakaian ===================== --}}
            <div class="card">
                <div class="card-header">Riwayat Pemakaian Kredit</div>
                <div class="card-body !p-0">
                    <table class="js-datatable w-full display"
                           data-dt-url="{{ route('admin.sina.data') }}"
                           data-dt-columns='@json($columns)'
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
        </div>
    @endif
@endsection

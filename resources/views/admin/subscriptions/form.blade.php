@extends('layouts.admin')

@section('title', $title)

@section('content')
    @php
        $action = $record
            ? route($routeName . '.update', $record->getRouteKey())
            : route($routeName . '.store');
        $selectedModules = old('module_keys', $record?->module_keys ?? []);
        $selectedModules = is_array($selectedModules) ? $selectedModules : [];
        $intervals = [
            'monthly' => 'Bulanan',
            'yearly' => 'Tahunan',
            'weekly' => 'Mingguan',
            'daily' => 'Harian',
        ];
        $currencies = ['IDR' => 'IDR', 'USD' => 'USD'];
    @endphp

    <div class="card max-w-5xl">
        <div class="card-header">{{ $record ? __('common.edit') : __('common.add') }} {{ $title }}</div>

        <form method="POST" action="{{ $action }}" class="card-body space-y-4">
            @csrf
            @if($record)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="form-label">Kode</label>
                    <input type="text" name="code" value="{{ old('code', $record?->code) }}" class="form-control" required>
                    @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $record?->name) }}" class="form-control" required>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control" required>
                        @foreach(['active' => 'Aktif', 'inactive' => 'Nonaktif'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $record?->status ?? 'active') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Periode penagihan</label>
                    <select name="billing_interval" class="form-control" required>
                        @foreach($intervals as $value => $label)
                            <option value="{{ $value }}" @selected(old('billing_interval', $record?->billing_interval ?? 'monthly') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('billing_interval') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="form-label">Deskripsi</label>
                <textarea name="description" rows="2" maxlength="255" class="form-control">{{ old('description', $record?->description) }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                <div>
                    <label class="form-label">Jumlah harga</label>
                    <input type="number" name="price_amount" min="0" step="0.01" value="{{ old('price_amount', $record?->price_amount ?? '0.00') }}" class="form-control" required>
                    @error('price_amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Mata uang</label>
                    <select name="currency" class="form-control" required>
                        @foreach($currencies as $value => $label)
                            <option value="{{ $value }}" @selected(old('currency', $record?->currency ?? 'IDR') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('currency') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Hari uji coba</label>
                    <input type="number" name="trial_days" min="0" value="{{ old('trial_days', $record?->trial_days ?? 0) }}" class="form-control" required>
                    @error('trial_days') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Batas kursi</label>
                    <input type="number" name="seat_limit" min="1" value="{{ old('seat_limit', $record?->seat_limit) }}" class="form-control">
                    @error('seat_limit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Kredit SINA per pasien per periode</label>
                    <input type="number" name="sina_credit_per_patient_per_period" min="0" value="{{ old('sina_credit_per_patient_per_period', $record?->sina_credit_per_patient_per_period ?? 0) }}" class="form-control" required>
                    @error('sina_credit_per_patient_per_period') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="space-y-1 text-xs text-slate-500">
                <p>Biarkan kosong untuk kursi tak terbatas.</p>
                <p>Kredit paket masuk ke wallet pasien saat saldo diperiksa atau chat dimulai. Kredit yang belum dipakai tetap terbawa.</p>
            </div>

            <fieldset>
                <legend class="form-label mb-1">Modul berlisensi</legend>
                <p class="text-xs text-slate-500 mb-2">Modul yang dilisensikan oleh paket ini. Modul berlisensi hanya dapat dipakai saat langganan tenant aktif.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2">
                    @foreach($modules as $key => $label)
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" name="module_keys[]" value="{{ $key }}" class="form-check"
                                   @checked(in_array((string) $key, array_map('strval', $selectedModules), true))>
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('module_keys') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                @error('module_keys.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </fieldset>

            <div class="flex items-center gap-2 pt-1">
                <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
                <a href="{{ route($routeName . '.index') }}" class="btn btn-secondary">{{ __('common.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection

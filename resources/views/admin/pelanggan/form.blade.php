@extends('layouts.admin')

@section('title', $title)

@section('content')
    @php
        $action = $record ? route($routeName . '.update', $record->getRouteKey()) : route($routeName . '.store');

        // Definisi field controller dipakai untuk mengambil opsi dinamis (role & relasi cabang).
        $fieldByName = collect($fields)->filter(fn ($f) => isset($f['name']))->keyBy('name');
        $val = fn (string $name, $default = null) => old($name, optional($record)->{$name} ?? $default);

        $sexOptions     = $fieldByName['sex']['options'] ?? ['male' => 'Male', 'female' => 'Female'];
        $countryOptions = $fieldByName['country']['options'] ?? ['id' => 'ID', 'my' => 'MY'];
        $roleOptions    = $fieldByName['role']['options'] ?? [];

        // Relasi cabang (karena tanpa lib, tabel cabangs dibaca langsung dengan label `name`).
        $cabangRows = collect();
        $cabangLabel = 'name';
        if ($rel = $fieldByName['cabang_id']['relation'] ?? null) {
            $cabangLabel = $rel['label'] ?? 'name';
            if (! empty($rel['model']) && class_exists($rel['model'])) {
                $cabangQuery = $rel['model']::query();
                if (! empty($rel['with'])) {
                    $cabangQuery->with($rel['with']);
                }
                $cabangRows = $cabangQuery->orderBy($cabangLabel)->get();
            }
        }

        $picture  = $val('picture');
        $isActive = old('is_active', $record ? (bool) $record->is_active : true);
        $fmtDate  = fn ($value) => $value ? \Illuminate\Support\Carbon::parse($value)->format('Y-m-d') : '';
        $fmtDt    = fn ($value) => $value ? \Illuminate\Support\Carbon::parse($value)->format('Y-m-d\TH:i') : '';
    @endphp

    <div class="card max-w-4xl">
        <div class="card-header">{{ $record ? __('common.edit') : __('common.add') }} {{ $title }}</div>

        <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="card-body space-y-6">
            @csrf
            @if($record)
                @method('PUT')
            @endif

            {{-- ==================== Identitas Utama ==================== --}}
            <section class="space-y-4">
                <h3 class="text-sm font-semibold text-slate-700 border-b border-slate-100 pb-2">{{ __('common.identitas_title') }}</h3>

                <div class="grid grid-cols-1 sm:grid-cols-[auto_1fr] gap-4 items-start">
                    {{-- Foto --}}
                    <div class="w-28">
                        <label class="form-label">{{ __('common.picture') }}</label>
                        @if($picture)
                            <div class="mb-2">
                                <button type="button" class="js-image-preview inline-block" data-full-src="{{ \Illuminate\Support\Facades\Storage::url($picture) }}">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($picture) }}" alt="" class="w-24 h-24 rounded-lg object-cover border border-slate-200 cursor-pointer hover:opacity-80">
                                </button>
                            </div>
                        @else
                            <div class="mb-2 w-24 h-24 rounded-lg bg-slate-50 border border-dashed border-slate-200 grid place-items-center text-slate-300">
                                <i data-lucide="image"></i>
                            </div>
                        @endif
                        <input type="file" name="picture" accept="image/*" class="form-control !text-xs">
                        @error('picture') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Identitas --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">{{ __('common.patient_number') }}</label>
                            <input type="text" name="patient_number" value="{{ $val('patient_number') }}" class="form-control">
                            @error('patient_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">{{ __('common.name') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ $val('name') }}" class="form-control" required>
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">{{ __('common.sex') }} <span class="text-red-500">*</span></label>
                            <select name="sex" class="form-control" required>
                                <option value="">-</option>
                                @foreach($sexOptions as $optKey => $optLabel)
                                    <option value="{{ $optKey }}" @selected((string) $val('sex') === (string) $optKey)>{{ $optLabel }}</option>
                                @endforeach
                            </select>
                            @error('sex') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">{{ __('common.birthday') }}</label>
                            <input type="date" name="date_of_birth" value="{{ $fmtDate($val('date_of_birth')) }}" class="form-control">
                            @error('date_of_birth') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </section>

            {{-- ==================== Kontak ==================== --}}
            <section class="space-y-4">
                <h3 class="text-sm font-semibold text-slate-700 border-b border-slate-100 pb-2">Kontak</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">{{ __('common.email') }}</label>
                        <input type="email" name="email" value="{{ $val('email') }}" class="form-control">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">{{ __('common.phone') }}</label>
                        <input type="text" name="phone" value="{{ $val('phone') }}" class="form-control">
                        @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            {{-- ==================== Alamat & Wilayah ==================== --}}
            <section class="space-y-4">
                <h3 class="text-sm font-semibold text-slate-700 border-b border-slate-100 pb-2">{{ __('common.region_title') }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="form-label">{{ __('common.city') }}</label>
                        <input type="text" name="city" value="{{ $val('city') }}" class="form-control">
                        @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">{{ __('common.region') }}</label>
                        <input type="text" name="region" value="{{ $val('region') }}" class="form-control">
                        @error('region') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">{{ __('common.country') }} <span class="text-red-500">*</span></label>
                        <select name="country" class="form-control" required>
                            <option value="">-</option>
                            @foreach($countryOptions as $optKey => $optLabel)
                                <option value="{{ $optKey }}" @selected((string) $val('country') === (string) $optKey)>{{ $optLabel }}</option>
                            @endforeach
                        </select>
                        @error('country') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            {{-- ==================== Status & Registrasi ==================== --}}
            <section class="space-y-4">
                <h3 class="text-sm font-semibold text-slate-700 border-b border-slate-100 pb-2">Status &amp; Registrasi</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="flex items-end pb-2">
                        <label class="flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="is_active" value="1" class="form-check" @checked($isActive)>
                            {{ __('common.active') }}
                        </label>
                    </div>
                    <div>
                        <label class="form-label">{{ __('common.date_register') }}</label>
                        <input type="datetime-local" name="registered_at" value="{{ $fmtDt($val('registered_at', now())) }}" class="form-control">
                        @error('registered_at') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Tanggal Arsip</label>
                        <div class="form-control bg-slate-50 text-slate-500">
                            {{ $record && $record->archived_at ? \Illuminate\Support\Carbon::parse($record->archived_at)->format('d M Y H:i') : '-' }}
                        </div>
                        {{-- Hanya tampilan: nilai lama tetap dikirim agar tidak terhapus saat simpan,
                             dan hanya diubah melalui tombol Arsipkan. --}}
                        <input type="hidden" name="archived_at" value="{{ $fmtDt($val('archived_at')) }}">
                        @error('archived_at') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            {{-- ==================== Akun & Akses ==================== --}}
            <section class="space-y-4">
                <h3 class="text-sm font-semibold text-slate-700 border-b border-slate-100 pb-2">{{ __('common.account') }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">{{ __('common.user_account') }}</label>
                        <select name="role" class="form-control">
                            <option value="">-</option>
                            @foreach($roleOptions as $optKey => $optLabel)
                                <option value="{{ $optKey }}" @selected((string) $val('role') === (string) $optKey)>{{ $optLabel }}</option>
                            @endforeach
                        </select>
                        <p class="text-slate-400 text-xs mt-1">{{ __('common.credential_hint') }}</p>
                        @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">{{ __('common.branch') }}</label>
                        <select name="cabang_id" class="form-control">
                            <option value="">-</option>
                            @foreach($cabangRows as $cabang)
                                <option value="{{ $cabang->id }}" @selected((string) $val('cabang_id') === (string) $cabang->id)>
                                    {{ $cabang->{$cabangLabel} }}
                                </option>
                            @endforeach
                        </select>
                        @error('cabang_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-100">
                <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
                <button type="submit" name="archive" value="1" class="btn btn-secondary"
                        onclick="return confirm('Arsipkan data pelanggan ini?');">
                    <i data-lucide="archive"></i> Arsipkan
                </button>
                <a href="{{ route($routeName . '.index') }}" class="btn btn-secondary">{{ __('common.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection

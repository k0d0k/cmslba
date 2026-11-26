@extends('layouts.admin')

@section('title', $title)

@section('content')
    @php
        $action = $record ? route($routeName . '.update', $record->getRouteKey()) : route($routeName . '.store');
    @endphp

    <div class="card max-w-4xl">
        <div class="card-header">{{ $record ? __('common.edit') : __('common.add') }} {{ $title }}</div>

        <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="card-body space-y-6">
            @csrf
            @if($record)
                @method('PUT')
            @endif

            {{-- SECTION 1: IDENTITAS UTAMA --}}
            <div class="pt-3 border-t border-slate-100 first:border-0 first:pt-0">
                <h3 class="text-sm font-semibold text-slate-700 mb-3">{{ __('common.identitas_title') }}</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                     {{-- Cabang --}}
                    <div>
                        <label class="form-label">{{ __('common.branch') }}</label>
                        @php $cabangVal = old('cabang_id', optional($record)->cabang_id); @endphp
                        <select name="cabang_id" class="form-control">
                            <option value="">-</option>
                            @foreach($cabangs ?? [] as $row)
                                <option value="{{ $row->id }}" @selected((string) $cabangVal === (string) $row->id)>
                                    {{ $row->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('cabang_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tanggal Registrasi --}}
                    <div>
                        <label class="form-label">{{ __('common.date_register') }}</label>
                        @php $regVal = old('registered_at', optional($record)->registered_at); @endphp
                      <input type="datetime-local" 
                            name="registered_at"
                            value="{{ $regVal ? \Illuminate\Support\Carbon::parse($regVal)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i') }}"
                            class="form-control">

                        @error('registered_at') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                   

                    {{-- Nomor Pasien / Pelanggan --}}
                    <div>
                        <label class="form-label">{{ __('common.patient_number') }}</label>
                        <input type="text" name="patient_number" value="{{ old('patient_number', optional($record)->patient_number) }}" class="form-control" placeholder="Generate by System" readonly>
                        @error('patient_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Nama --}}
                    <div>
                        <label class="form-label">{{ __('common.name') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', optional($record)->name) }}" class="form-control" required>
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Jenis Kelamin --}}
                    <div>
                        <label class="form-label">{{ __('common.sex') }} <span class="text-red-500">*</span></label>
                        @php $sexVal = old('sex', optional($record)->sex); @endphp
                        <select name="sex" class="form-control" required>
                            <option value="">{{ __('common.select') }}</option>
                            <option value="male" @selected((string) $sexVal === 'male')>Male</option>
                            <option value="female" @selected((string) $sexVal === 'female')>Female</option>
                        </select>
                        @error('sex') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tanggal Lahir --}}
                    <div>
                        <label class="form-label">{{ __('common.birthday') }}</label>
                        @php $dobVal = old('date_of_birth', optional($record)->date_of_birth); @endphp
                        <input type="date" name="date_of_birth" value="{{ $dobVal ? \Illuminate\Support\Carbon::parse($dobVal)->format('Y-m-d') : '' }}" class="form-control">
                        @error('date_of_birth') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="form-label">{{ __('common.email') }}</label>
                        <input type="email" name="email" value="{{ old('email', optional($record)->email) }}" class="form-control">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Telepon --}}
                    <div>
                        <label class="form-label">{{ __('common.phone') }}</label>
                        <input type="text" name="phone" value="{{ old('phone', optional($record)->phone) }}" class="form-control">
                        @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- SECTION 2: ALAMAT & WILAYAH --}}
            <div class="pt-3 border-t border-slate-100">
                <h3 class="text-sm font-semibold text-slate-700 mb-3">{{ __('common.region_title') }}</h3>
                 <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                    <div>
                        <label class="form-label">{{ __('common.address') }}</label>
                        <input type="text" name="city" value="{{ old('address', optional($record)->address) }}" class="form-control">
                        @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="form-label">{{ __('common.city') }}</label>
                        <input type="text" name="city" value="{{ old('city', optional($record)->city) }}" class="form-control">
                        @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">{{ __('common.region') }}</label>
                        <input type="text" name="region" value="{{ old('region', optional($record)->region) }}" class="form-control">
                        @error('region') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">{{ __('common.country') }}</label>
                          @php $countryVal = old('country', optional($record)->country); @endphp
                        <select name="country" class="form-control" required>
                            <option value="">{{ __('common.select') }}</option>
                            <option value="id" @selected((string) $countryVal === 'id')>ID</option>
                            <option value="my" @selected((string) $countryVal === 'my')>MY</option>
                        </select>
                        @error('country') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- SECTION 3: AKUN, CABANG & STATUS --}}
            <div class="pt-3 border-t border-slate-100">
                <h3 class="text-sm font-semibold text-slate-700 mb-3">{{ __('common.statusfoto_title') }}</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Akun User / Role --}}
                        <input type="hidden" name="region" value="{{ old('role', optional(optional($record)->user?->roles?->first())->id) }}" class="form-control">

                      
                    {{-- Foto --}}
                    <div>
                        <label class="form-label">{{ __('common.picture') }}</label>
                        @php $picVal = old('picture', optional($record)->picture); @endphp
                        @if($picVal)
                            <div class="mb-2">
                                <button type="button" class="js-image-preview inline-block" data-full-src="{{ \Illuminate\Support\Facades\Storage::url($picVal) }}">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($picVal) }}" alt="" class="w-20 h-20 rounded-lg object-cover border border-slate-200 cursor-pointer hover:opacity-80">
                                </button>
                            </div>
                        @endif
                        <input type="file" name="picture" accept="image/*" class="form-control">
                        @error('picture') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div >
                        <label class="form-label">{{ __('common.status') }}</label>

                        <input type="hidden" name="is_active" value="0">
                        {{ $activeVal=old('city', optional($record)->city);}}
                        <label class="inline-flex items-center gap-2 mt-1">
                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                @checked((bool) $activeVal)
                                class="rounded border-slate-300"
                            >
                            <span class="text-sm text-slate-700">
                                {{ __('common.active') }}
                            </span>
                        </label>

                        @error('is_active')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
            </div>
            <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                    <div>
                        <label class="form-label">{{ __('common.notes') }}</label>
                        <input type="text" name="city" value="{{ old('notes', optional($record)->notes) }}" class="form-control">
                        @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            <div class="grid grid-cols-1 md:grid-cols-1 gap-4">

              <div class="flex items-center gap-2 pt-2">
                <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
                <a href="{{ route($routeName . '.index') }}" class="btn btn-secondary">{{ __('common.cancel') }}</a>
            </div>
            </div>
</form>
</div>
</div>
                   


  @endsection                
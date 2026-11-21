@extends('layouts.admin')

@section('title', __('Setting Aplikasi'))

@section('content')
    <div class="card max-w-3xl">
        <div class="card-header">Setting Aplikasi</div>

        <form method="POST" action="{{ route('admin.setting-app.update') }}" enctype="multipart/form-data" class="card-body space-y-4">
            @csrf

            <div>
                <label class="form-label">Nama Aplikasi</label>
                <input type="text" name="app_name" value="{{ old('app_name', $settings['app_name']) }}" class="form-control" required>
                @error('app_name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="form-label">Logo</label>
                @if($settings['logo'])
                    <div class="mb-2">
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($settings['logo']) }}" alt="Logo" class="h-16 rounded-lg object-contain border border-slate-200 bg-white p-1">
                    </div>
                @endif
                <input type="file" name="logo" accept="image/*" class="form-control">
                @error('logo')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="form-label">Default Bahasa</label>
                    <select name="default_locale" class="form-control">
                        @foreach(config('lbacloud.locales') as $code => $loc)
                            <option value="{{ $code }}" @selected(old('default_locale', $settings['default_locale']) === $code)>{{ $loc['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">{{ __('common.phone') }}</label>
                    <input type="text" name="phone" value="{{ old('phone', $settings['phone']) }}" class="form-control">
                </div>
            </div>

            <div>
                <label class="form-label">{{ __('common.email') }}</label>
                <input type="email" name="email" value="{{ old('email', $settings['email']) }}" class="form-control">
            </div>

            <div>
                <label class="form-label">{{ __('common.address') }}</label>
                <textarea name="address" rows="2" class="form-control">{{ old('address', $settings['address']) }}</textarea>
            </div>

            <div>
                <label class="form-label">Map</label>
                <textarea name="map" rows="3" class="form-control" placeholder="Tempel kode embed Google Maps atau URL lokasi">{{ old('map', $settings['map']) }}</textarea>
                @if($settings['map'] && \Illuminate\Support\Str::startsWith(trim($settings['map']), '<iframe'))
                    <div class="mt-2 overflow-hidden rounded-lg border border-slate-200">
                        {!! $settings['map'] !!}
                    </div>
                @endif
            </div>

            @if($canUpdate)
                <div class="flex items-center gap-2 pt-2">
                    <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
                </div>
            @endif
        </form>
    </div>
@endsection

@extends('layouts.guest')

@section('title', __('auth.register_title'))

@section('content')
    <h2 class="text-lg font-semibold text-slate-800 mb-1">{{ __('auth.register_title') }}</h2>
    <p class="text-sm text-slate-500 mb-5">{{ __('auth.register_subtitle') }}</p>

    <form method="POST" action="{{ route('mitra.register.store') }}" class="space-y-4">
        @csrf

        <div>
            <label class="form-label">{{ __('auth.mitra_name') }}</label>
            <input type="text" name="mitra_name" value="{{ old('mitra_name') }}" class="form-control" required>
            <p class="text-xs text-slate-400 mt-1">{{ __('auth.frontpage_public') }}: {{ url('/') }}/nama-mitra</p>
        </div>

        <div>
            <label class="form-label">{{ __('auth.owner_name') }}</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
        </div>

        <div>
            <label class="form-label">{{ __('auth.email') }}</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="form-label">{{ __('auth.password_label') }}</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div>
                <label class="form-label">{{ __('auth.confirm_label') }}</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
        </div>

        <div>
            <label class="form-label">{{ __('common.language') }}</label>
            <select name="locale" class="form-control">
                @foreach(config('lbacloud.locales') as $code => $loc)
                    <option value="{{ $code }}" @selected(old('locale', 'id') === $code)>{{ $loc['label'] }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary w-full justify-center">{{ __('common.register') }}</button>
    </form>

    <div class="mt-5 pt-4 border-t border-slate-100 text-center text-sm text-slate-500">
        {{ __('auth.have_account') }}
        <a href="{{ route('login') }}" class="font-semibold" style="color: var(--color-primary)">{{ __('common.login') }}</a>
    </div>
@endsection

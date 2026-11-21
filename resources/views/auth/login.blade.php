@extends('layouts.guest')

@section('title', __('auth.login_title'))

@section('content')
    <h2 class="text-lg font-semibold text-slate-800 mb-1">{{ __('auth.login_title') }}</h2>
    <p class="text-sm text-slate-500 mb-5">{{ __('common.welcome') }} kembali.</p>

    <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
        @csrf

        <div>
            <label class="form-label">{{ __('auth.email') }}</label>
            <input type="text" name="email" value="{{ old('email') }}" class="form-control" autofocus required>
        </div>

        <div>
            <label class="form-label">{{ __('auth.password_label') }}</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="remember" class="form-check">
            {{ __('auth.remember') }}
        </label>

        <button type="submit" class="btn btn-primary w-full justify-center">{{ __('common.login') }}</button>
    </form>

    <div class="mt-5 pt-4 border-t border-slate-100 text-center text-sm text-slate-500">
        {{ __('common.mitra') }} baru?
        <a href="{{ route('mitra.register') }}" class="font-semibold" style="color: var(--color-primary)">{{ __('common.register') }}</a>
    </div>

    <div class="mt-4 rounded-lg bg-slate-50 border border-slate-200 p-3 text-xs text-slate-500 leading-relaxed">
        <div class="font-semibold text-slate-600 mb-1">Akun demo</div>
        Admin Principal: <b>admin@lbacloud.test</b> / <b>password</b><br>
        User Principal: <b>user@lbacloud.test</b> / <b>password</b><br>
        Pemilik Mitra: <b>pemilik@demo.test</b> / <b>password</b><br>
        Staff Admin: <b>staff@demo.test</b> / <b>password</b>
    </div>
@endsection

@extends('layouts.guest')

@section('title', 'Masa Berlangganan Habis')

@section('content')
    <div class="text-center">
        <div class="w-14 h-14 rounded-full grid place-items-center mx-auto mb-3" style="background: #fef3c7; color: #d97706;">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        </div>
        <h2 class="text-lg font-semibold text-slate-800 mb-1">Masa Berlangganan Habis</h2>
        <p class="text-sm text-slate-500 mb-5">
            Segera perpanjang masa langganan anda untuk dapat kembali mengakses akun.
        </p>

        <a href="{{ route('login') }}" class="btn btn-primary w-full justify-center">Kembali ke Login</a>

        <p class="text-xs text-slate-400 mt-4">
            Hubungi admin / principal untuk memperpanjang masa berlangganan.
        </p>
    </div>
@endsection

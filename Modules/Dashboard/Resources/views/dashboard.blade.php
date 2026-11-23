@extends('layouts.admin')

@section('title', __('common.dashboard'))

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        @foreach($cards as $card)
            <div class="card p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs uppercase text-slate-400 font-semibold">{{ $card['label'] }}</div>
                        <div class="text-3xl font-bold text-slate-800 mt-1">{{ $card['value'] }}</div>
                    </div>
                    <div class="w-3 h-10 rounded-full" style="background: {{ $card['color'] }}"></div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="card lg:col-span-2">
            <div class="card-header">{{ __('common.welcome') }}, {{ auth()->user()->name }}</div>
            <div class="card-body text-sm text-slate-600 space-y-2">
                <p>{!! __('dashboard.boilerplate') !!}</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li>{!! __('dashboard.bullet_menu') !!}</li>
                    <li>{!! __('dashboard.bullet_assign') !!}</li>
                    <li>{!! __('dashboard.bullet_slug') !!}</li>
                    <li>{{ __('dashboard.bullet_generator') }}</li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header">{{ __('common.active_mitra') }}</div>
            <div class="card-body text-sm text-slate-600">
                @if($activeMitra)
                    <div class="font-semibold text-slate-800">{{ $activeMitra->name }}</div>
                    <div class="text-xs text-slate-400 mb-3">{{ $activeMitra->db_name }}</div>
                    <div class="flex items-center justify-between border-t border-slate-100 py-2">
                        <span>{{ __('dashboard.card_branch') }}</span><span class="font-semibold">{{ $tenantStats['cabang'] }}</span>
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-100 py-2">
                        <span>{{ __('dashboard.card_customer') }}</span><span class="font-semibold">{{ $tenantStats['pelanggan'] }}</span>
                    </div>
                    <a href="{{ route('frontpage.mitra', $activeMitra->slug) }}" target="_blank" class="btn btn-secondary btn-sm mt-3 w-full justify-center">
                        {{ __('dashboard.view_frontpage') }}
                    </a>
                @else
                    <p class="text-slate-400">{{ __('messages.no_data') }}</p>
                @endif
            </div>
        </div>
    </div>
@endsection

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $mitra->name }} &middot; {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @php
        $skin = \App\Models\Skin::where('code', $mitra->skin)->first();
    @endphp
    <style>:root { --color-primary: {{ $skin->primary ?? '#465fff' }}; }</style>
</head>
<body class="bg-slate-50">
    <header class="text-white" style="background: linear-gradient(135deg, var(--color-primary), #1c2434)">
        <div class="max-w-5xl mx-auto px-4 py-12">
            <div class="text-sm opacity-80">{{ __('common.welcome') }}</div>
            <h1 class="text-3xl font-bold mt-1">{{ $mitra->name }}</h1>
            <p class="opacity-80 mt-2">{{ __('frontpage.mitra_frontpage') }} &middot; {{ __('frontpage.db_label') }} <code>{{ $mitra->db_name }}</code></p>
            <div class="flex gap-2 mt-5">
                <a href="{{ route('login') }}" class="btn" style="background:#fff; color: var(--color-primary)">{{ __('common.login') }}</a>
                <a href="{{ route('mitra.register') }}" class="btn btn-secondary">{{ __('common.register') }}</a>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 -mt-8">
        @if(session('success'))
            <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="card p-5">
                <div class="text-xs uppercase text-slate-400 font-semibold">{{ __('frontpage.mitra_status') }}</div>
                <div class="text-xl font-bold text-slate-800 mt-1">{{ ucfirst($mitra->status) }}</div>
            </div>
            <div class="card p-5">
                <div class="text-xs uppercase text-slate-400 font-semibold">{{ __('frontpage.branch_count') }}</div>
                <div class="text-xl font-bold text-slate-800 mt-1">{{ $cabangCount }}</div>
            </div>
            <div class="card p-5">
                <div class="text-xs uppercase text-slate-400 font-semibold">{{ __('frontpage.customer_count') }}</div>
                <div class="text-xl font-bold text-slate-800 mt-1">{{ $pelangganCount }}</div>
            </div>
        </div>

        <div class="card mt-4 p-6 text-sm text-slate-600">
            <p>{{ __('frontpage.public_note', ['url' => url('/' . $mitra->slug), 'db' => $mitra->db_name]) }}</p>
        </div>
    </main>

    <footer class="max-w-5xl mx-auto px-4 py-8 text-xs text-slate-400">
        &copy; {{ date('Y') }} {{ $mitra->name }} &middot; {{ config('app.name') }}
    </footer>
</body>
</html>

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') &middot; {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen grid place-items-center p-4" style="background: linear-gradient(135deg,#1c2434,#334155)">
    <div class="w-full max-w-md">
        <div class="text-center mb-4">
            <div class="w-12 h-12 rounded-xl grid place-items-center font-bold text-white mx-auto mb-2" style="background: var(--color-primary)">L</div>
            <h1 class="text-white font-semibold">{{ config('app.name') }}</h1>
        </div>

        <div class="card p-6">
            @if(session('success'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 px-3 py-2 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 px-3 py-2 text-sm">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>

        <div class="text-center text-xs text-slate-300 mt-4">
            &copy; {{ date('Y') }} {{ config('app.name') }} &middot; LBA Cloud Boilerplate
        </div>
    </div>
</body>
</html>

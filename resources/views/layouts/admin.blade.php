@php
    $menuService = app(\App\Services\MenuService::class);
    $currentUser = auth()->user();
    $sidebarItems = $currentUser ? $menuService->sidebar($currentUser) : collect();

    $activeSkinCode = session('skin', $currentUser->skin ?? 'default');
    $activeSkin = \App\Models\Skin::where('code', $activeSkinCode)->first();
    $skinPrimary = $activeSkin->primary ?? '#087f80';
    $skinSidebar = $activeSkin->sidebar ?? '#14263d';

    $activeMitra = app()->bound('active_mitra') ? app('active_mitra') : null;
    $isPrincipal = $currentUser?->isPrincipal() ?? false;
    $currentRoute = request()->route()?->getName();
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') &middot; {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --color-primary: {{ $skinPrimary }};
            --color-sidebar: {{ $skinSidebar }};
        }
    </style>
</head>
<body>
<div class="min-h-screen flex">

    {{-- ==================== Sidebar ==================== --}}
    <aside id="sidebar" class="sidebar w-64 shrink-0 min-h-screen p-3 hidden md:block">
        <div class="flex items-center gap-2 px-2 py-3 mb-2">
            <div class="w-8 h-8 rounded-lg grid place-items-center font-bold text-white" style="background: var(--color-primary)">L</div>
            <span class="font-semibold text-white text-sm">{{ config('app.name') }}</span>
        </div>

        <nav class="flex flex-col gap-0.5">
            @include('layouts.partials.menu-item', ['items' => $sidebarItems, 'level' => 0])
        </nav>
    </aside>

    {{-- ==================== Main ==================== --}}
    <div class="flex-1 min-w-0 flex flex-col">

        {{-- Topbar --}}
        <header class="bg-white border-b border-slate-200 h-16 flex items-center gap-3 px-4 sticky top-0 z-20">
            <button type="button" data-sidebar-toggle class="md:hidden btn btn-secondary btn-sm">&#9776;</button>

            <div class="font-semibold text-slate-700">@yield('title', 'Dashboard')</div>

            <div class="ml-auto flex items-center gap-2">

                {{-- Mitra switcher (hanya principal) --}}
                @if($isPrincipal)
                    <form method="POST" action="{{ route('admin.preference.mitra') }}" class="hidden lg:block">
                        @csrf
                        <select name="mitra_id" onchange="this.form.submit()" class="form-control !py-1.5 !text-xs">
                            @foreach(\App\Models\Mitra::orderBy('name')->get() as $m)
                                <option value="{{ $m->id }}" @selected($activeMitra && $activeMitra->id === $m->id)>
                                    {{ __('common.active_mitra') }}: {{ $m->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif

                {{-- Bahasa --}}
                <div class="relative">
                    <button type="button" data-dropdown-toggle="dd-locale" class="btn btn-secondary btn-sm">
                        {{ strtoupper(app()->getLocale()) }}
                    </button>
                    <div id="dd-locale" data-dropdown class="hidden absolute right-0 mt-1 w-40 card p-1 z-30">
                        @foreach(config('lbacloud.locales') as $code => $loc)
                            <form method="POST" action="{{ route('admin.preference.locale') }}">
                                @csrf
                                <input type="hidden" name="locale" value="{{ $code }}">
                                <button type="submit" class="w-full text-left px-3 py-2 rounded text-sm hover:bg-slate-100 {{ app()->getLocale() === $code ? 'font-semibold' : '' }}">
                                    {{ $loc['label'] }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>

                {{-- Skin --}}
                <div class="relative">
                    <button type="button" data-dropdown-toggle="dd-skin" class="btn btn-secondary btn-sm">{{ __('common.skin') }}</button>
                    <div id="dd-skin" data-dropdown class="hidden absolute right-0 mt-1 w-40 card p-1 z-30">
                        @foreach(\App\Models\Skin::where('is_active', true)->orderBy('id')->get() as $sk)
                            <form method="POST" action="{{ route('admin.preference.skin') }}">
                                @csrf
                                <input type="hidden" name="skin" value="{{ $sk->code }}">
                                <button type="submit" class="w-full text-left px-3 py-2 rounded text-sm hover:bg-slate-100 flex items-center gap-2 {{ $activeSkinCode === $sk->code ? 'font-semibold' : '' }}">
                                    <span class="w-3 h-3 rounded-full inline-block" style="background: {{ $sk->primary }}"></span>
                                    {{ $sk->label }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>

                {{-- User --}}
                <div class="relative">
                    <button type="button" data-dropdown-toggle="dd-user" class="btn btn-secondary btn-sm">
                        {{ $currentUser?->name }}
                    </button>
                    <div id="dd-user" data-dropdown class="hidden absolute right-0 mt-1 w-56 card p-2 z-30">
                        <div class="px-3 py-2 text-xs text-slate-500 border-b border-slate-100 mb-1">
                            {{ $currentUser?->email }}<br>
                            @foreach($currentUser?->roles ?? [] as $r)
                                <span class="badge badge-secondary mt-1">{{ $r->name }}</span>
                            @endforeach
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-3 py-2 rounded text-sm hover:bg-slate-100 text-red-600">{{ __('common.logout') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash + Konten --}}
        <main class="p-4 flex-1">
            @if(session('success'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="px-4 py-3 text-xs text-slate-500 border-t border-slate-200 bg-white">
            &copy; {{ date('Y') }} {{ config('app.name') }} &middot; LBA Cloud Boilerplate
        </footer>
    </div>
</div>
</body>
</html>

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

    // Daftar cabang pada database mitra yang sedang aktif (untuk pemilih cabang).
    $cabangs = collect();
    if ($activeMitra) {
        try {
            $cabangs = \Illuminate\Support\Facades\DB::connection('tenant')
                ->table('cabangs')->orderBy('name')->get();
        } catch (\Throwable $e) {
            $cabangs = collect();
        }
    }
    $activeCabangId = session('active_cabang_id');

    // Identitas aplikasi (logo & footer) dari tabel settings grup `app`.
    $appLogo = \App\Models\Setting::get('logo', null, 'app');
    $appFooter = \App\Models\Setting::get('footer', null, 'app');
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') &middot; {{ config('app.name') }}</title>
    @if($appLogo)
        <link rel="icon" href="{{ \Illuminate\Support\Facades\Storage::url($appLogo) }}">
    @endif
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
    <aside id="sidebar" class="sidebar w-64 shrink-0 min-h-screen p-3">
        <div class="flex items-center gap-2 px-2 py-3 mb-2">
            @if($appLogo)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($appLogo) }}" alt="{{ config('app.name') }}" class="w-8 h-8 rounded-lg object-contain bg-white/10 p-0.5">
            @else
                <div class="w-8 h-8 rounded-lg grid place-items-center font-bold text-white" style="background: var(--color-primary)">L</div>
            @endif
            <span class="font-semibold text-white text-sm">{{ config('app.name') }}</span>
        </div>

        <nav class="flex flex-col gap-0.5">
            @include('layouts.partials.menu-item', ['items' => $sidebarItems, 'level' => 0])
        </nav>
    </aside>

    {{-- Backdrop untuk sidebar mobile --}}
    <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/50 z-50"></div>

    {{-- ==================== Main ==================== --}}
    <div class="flex-1 min-w-0 flex flex-col">

        {{-- Topbar --}}
        <header class="bg-white border-b border-slate-200 h-16 flex items-center gap-3 px-4 sticky top-0 z-20">
            <button type="button" data-sidebar-toggle class="btn btn-secondary btn-sm" title="{{ __('common.sidebar_toggle') }}">
                <i data-lucide="menu"></i>
            </button>

            <div class="font-semibold text-slate-700 truncate">@yield('title', 'Dashboard')</div>

            <div class="ml-auto flex items-center gap-2">

                {{-- Mitra switcher (hanya principal) --}}
                @if($isPrincipal)
                    <form method="POST" action="{{ route('admin.preference.mitra') }}" class="hidden lg:block">
                        @csrf
                        <select name="mitra_id" onchange="this.form.submit()" class="form-control !py-1.5 !text-xs" title="{{ __('common.active_mitra') }}">
                            @foreach(\App\Models\Mitra::orderBy('name')->get() as $m)
                                <option value="{{ $m->id }}" @selected($activeMitra && $activeMitra->id === $m->id)>
                                    {{ __('common.active_mitra') }}: {{ $m->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif

                {{-- Cabang switcher (database mitra aktif) --}}
                @if($cabangs->count())
                    <form method="POST" action="{{ route('admin.preference.cabang') }}" class="hidden lg:block">
                        @csrf
                        <select name="cabang_id" onchange="this.form.submit()" class="form-control !py-1.5 !text-xs" title="{{ __('common.active_cabang') }}">
                            <option value="">{{ __('common.all_branches') }}</option>
                            @foreach($cabangs as $c)
                                <option value="{{ $c->id }}" @selected((string) $activeCabangId === (string) $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </form>
                @endif

                {{-- Bahasa --}}
                <div class="relative">
                    <button type="button" data-dropdown-toggle="dd-locale" class="btn btn-secondary btn-sm" title="{{ __('common.language') }}">
                        <i data-lucide="globe"></i>
                        <span class="hidden sm:inline">{{ strtoupper(app()->getLocale()) }}</span>
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
                    <button type="button" data-dropdown-toggle="dd-skin" class="btn btn-secondary btn-sm" title="{{ __('common.skin') }}">
                        <i data-lucide="palette"></i>
                        <span class="w-3 h-3 rounded-full inline-block" style="background: {{ $skinPrimary }}"></span>
                    </button>
                    <div id="dd-skin" data-dropdown class="hidden absolute right-0 mt-1 w-44 card p-1 z-30">
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
                        @if($currentUser?->avatar)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($currentUser->avatar) }}" alt="" class="w-6 h-6 rounded-full object-cover">
                        @else
                            <span class="w-6 h-6 rounded-full grid place-items-center text-white text-xs font-semibold" style="background: var(--color-primary)">
                                {{ strtoupper(substr($currentUser?->name ?? 'U', 0, 1)) }}
                            </span>
                        @endif
                        <span class="hidden sm:inline">{{ $currentUser?->name }}</span>
                        <i data-lucide="chevron-down"></i>
                    </button>
                    <div id="dd-user" data-dropdown class="hidden absolute right-0 mt-1 w-60 card p-2 z-30">
                        <div class="px-3 py-2 text-xs text-slate-500 border-b border-slate-100 mb-1">
                            <div class="font-semibold text-slate-700">{{ $currentUser?->name }}</div>
                            {{ $currentUser?->email }}<br>
                            @foreach($currentUser?->roles ?? [] as $r)
                                <span class="badge badge-secondary mt-1">{{ config("lbacloud.roles.{$r->name}.label", $r->name) }}</span>
                            @endforeach
                        </div>
                        <a href="{{ route('admin.account.profile') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded text-sm hover:bg-slate-100">
                            <i data-lucide="user"></i>
                            {{ __('common.profile') }}
                        </a>
                        <a href="{{ route('admin.account.password') }}" class="w-full flex items-center gap-2 px-3 py-2 rounded text-sm hover:bg-slate-100">
                            <i data-lucide="lock"></i>
                            {{ __('common.change_password') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 text-left px-3 py-2 rounded text-sm hover:bg-slate-100 text-red-600">
                                <i data-lucide="log-out"></i>
                                {{ __('common.logout') }}
                            </button>
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

            @if(session('error'))
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm">
                    {{ session('error') }}
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
            @if($appFooter)
                {{ $appFooter }}
            @else
                &copy; {{ date('Y') }} {{ config('app.name') }} &middot; LBA Cloud Boilerplate
            @endif
        </footer>
    </div>
</div>
</body>
</html>

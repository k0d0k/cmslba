<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $seo = $content['seo'] ?? [];
        $seoTitle = $seo['title'] ?: $brandName;
        $seoDesc  = $seo['description'] ?? '';
    @endphp
    <title>{{ $seoTitle }}</title>
    @if($seoDesc)<meta name="description" content="{{ $seoDesc }}">@endif
    <meta property="og:title" content="{{ $seoTitle }}">
    @if($seoDesc)<meta property="og:description" content="{{ $seoDesc }}">@endif
    @if(!empty($seo['og_image']))<meta property="og:image" content="{{ $seo['og_image'] }}">@endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="tpl-{{ $template }} bg-white text-slate-800">

    @if($isPreview)
        <div class="bg-amber-100 text-amber-900 text-center text-xs py-1.5 px-4">
            Pratinjau draf — halaman ini belum dipublikasikan.
        </div>
    @endif

    {{-- ============ Nav ============ --}}
    <header class="sticky top-0 z-30 bg-white/90 backdrop-blur border-b border-slate-200">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center gap-4">
            <a href="{{ url('/') }}" class="flex items-center gap-2 font-semibold">
                @if($mitra?->logo)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($mitra->logo) }}" alt="{{ $brandName }}" class="w-8 h-8 rounded-lg object-contain">
                @else
                    <span class="w-8 h-8 rounded-lg grid place-items-center font-bold text-white" style="background: var(--color-primary)">{{ strtoupper(substr($brandName, 0, 1)) }}</span>
                @endif
                <span>{{ $brandName }}</span>
            </a>

            <nav class="hidden md:flex items-center gap-6 text-sm text-slate-600 ml-6">
                @if(!empty($content['services']))<a href="#layanan" class="hover:text-slate-900">Layanan</a>@endif
                @if(!empty($content['features']))<a href="#fitur" class="hover:text-slate-900">Fitur</a>@endif
                @if(!empty($content['faq']))<a href="#faq" class="hover:text-slate-900">FAQ</a>@endif
                @if(array_filter($content['contact'] ?? []))<a href="#kontak" class="hover:text-slate-900">Kontak</a>@endif
            </nav>

            <div class="ml-auto flex items-center gap-2">
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-secondary">{{ __('common.login') }}</a>
                    <a href="{{ route('mitra.register') }}" class="btn btn-primary">{{ __('common.register') }}</a>
                @endauth
            </div>
        </div>
    </header>

    {{-- ============ Hero ============ --}}
    @php $hero = $content['hero'] ?? []; @endphp
    <section class="fp-hero text-white">
        <div class="max-w-6xl mx-auto px-4 py-20 text-center">
            @if(!empty($hero['eyebrow']))
                <div class="text-xs sm:text-sm uppercase tracking-widest opacity-80 mb-3">{{ $hero['eyebrow'] }}</div>
            @endif
            <h1 class="text-3xl sm:text-5xl font-bold leading-tight max-w-3xl mx-auto">{{ $hero['title'] ?? '' }}</h1>
            @if(!empty($hero['subtitle']))
                <p class="max-w-2xl mx-auto mt-5 opacity-90 text-sm sm:text-base">{{ $hero['subtitle'] }}</p>
            @endif
            <div class="flex flex-wrap gap-3 justify-center mt-8">
                @php
                    $btnUrl = !empty($hero['button_url']) ? $hero['button_url'] : route('login');
                    $btnText = !empty($hero['button_text']) ? $hero['button_text'] : __('common.login');
                @endphp
                <a href="{{ $btnUrl }}" class="btn" style="background:#fff; color: var(--color-primary)">{{ $btnText }}</a>
                <a href="{{ route('mitra.register') }}" class="btn" style="background: rgba(255,255,255,.15); color:#fff;">{{ __('common.register') }}</a>
            </div>
        </div>
    </section>

    <main class="max-w-6xl mx-auto px-4">

        {{-- ============ Layanan ============ --}}
        @if(!empty($content['services']))
            <section id="layanan" class="py-16">
                <h2 class="text-2xl font-bold text-center">Layanan</h2>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 mt-8">
                    @foreach($content['services'] as $item)
                        <div class="card p-6">
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-lg">{{ $item['title'] ?? '' }}</h3>
                                @if(!empty($item['tag']))<span class="badge badge-secondary">{{ $item['tag'] }}</span>@endif
                            </div>
                            <p class="text-sm text-slate-600 mt-2">{{ $item['description'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ============ Fitur ============ --}}
        @if(!empty($content['features']))
            <section id="fitur" class="py-16 border-t border-slate-100">
                <h2 class="text-2xl font-bold text-center">Fitur</h2>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4 mt-8">
                    @foreach($content['features'] as $item)
                        <div class="p-5 rounded-xl bg-slate-50 border border-slate-100">
                            <h3 class="font-semibold">{{ $item['title'] ?? '' }}</h3>
                            <p class="text-sm text-slate-600 mt-2">{{ $item['description'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ============ Media ============ --}}
        @if(!empty($media))
            <section class="py-16 border-t border-slate-100">
                <h2 class="text-2xl font-bold text-center">Galeri</h2>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 mt-8">
                    @foreach($media as $m)
                        <figure class="rounded-xl overflow-hidden border border-slate-100 bg-slate-50">
                            <img src="{{ $m['url'] }}" alt="{{ $m['alt'] ?? '' }}" class="w-full h-48 object-cover">
                        </figure>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ============ FAQ ============ --}}
        @if(!empty($content['faq']))
            <section id="faq" class="py-16 border-t border-slate-100">
                <h2 class="text-2xl font-bold text-center">FAQ</h2>
                <div class="max-w-3xl mx-auto mt-8 space-y-3">
                    @foreach($content['faq'] as $item)
                        <details class="card p-5">
                            <summary class="font-semibold cursor-pointer">{{ $item['question'] ?? '' }}</summary>
                            <p class="text-sm text-slate-600 mt-2">{{ $item['answer'] ?? '' }}</p>
                        </details>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ============ Kontak ============ --}}
        @php $contact = array_filter($content['contact'] ?? []); @endphp
        @if($contact)
            <section id="kontak" class="py-16 border-t border-slate-100">
                <h2 class="text-2xl font-bold text-center">Kontak</h2>
                <div class="max-w-xl mx-auto mt-6 text-sm text-slate-600 space-y-2 text-center">
                    @if(!empty($contact['email']))<div>Email: <a class="underline" href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a></div>@endif
                    @if(!empty($contact['phone']))<div>Telepon: {{ $contact['phone'] }}</div>@endif
                    @if(!empty($contact['address']))<div>{{ $contact['address'] }}</div>@endif
                </div>
            </section>
        @endif
    </main>

    <footer class="border-t border-slate-200 py-8 text-center text-xs text-slate-400">
        &copy; {{ date('Y') }} {{ $brandName }} &middot; {{ config('app.name') }}
    </footer>
</body>
</html>

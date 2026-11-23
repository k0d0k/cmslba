<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $seo = $content['seo'] ?? [];
        $seoTitle = $seo['title'] ?: $brandName;
        $seoDesc  = $seo['description'] ?? '';
        $hero     = $content['hero'] ?? [];
        $heroImage = $hero['image'] ?? '';
        $services = $content['services'] ?? [];
        $features = $content['features'] ?? [];
        $faqs     = $content['faq'] ?? [];
        $contactRaw = $content['contact'] ?? [];
        $contact  = array_filter(['email' => $contactRaw['email'] ?? '', 'phone' => $contactRaw['phone'] ?? '', 'address' => $contactRaw['address'] ?? '']);

        // Embed Google Maps: terima kode <iframe> dari Google Maps atau sekadar URL embed.
        $mapEmbed  = trim($contactRaw['map_embed'] ?? '');
        $mapIframe = '';
        if ($mapEmbed !== '') {
            if (stripos($mapEmbed, '<iframe') !== false && preg_match('/<iframe\b[^>]*>(?:.*?<\/iframe>)?/is', $mapEmbed, $m)) {
                $mapIframe = $m[0];
            } else {
                $mapIframe = '<iframe src="' . e($mapEmbed) . '" width="100%" height="380" style="border:0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>';
            }
        }
        $tagClass = fn ($tag) => (preg_match('/tersedia|available/i', (string) $tag)) ? 'fp-badge--available' : 'fp-badge--planned';
        $heroBtnUrl  = ! empty($hero['button_url']) ? $hero['button_url'] : route('login');
        $heroBtnText = ! empty($hero['button_text']) ? $hero['button_text'] : __('common.login');
    @endphp
    <title>{{ $seoTitle }}</title>
    @if($seoDesc)<meta name="description" content="{{ $seoDesc }}">@endif
    <meta property="og:title" content="{{ $seoTitle }}">
    @if($seoDesc)<meta property="og:description" content="{{ $seoDesc }}">@endif
    @if(!empty($seo['og_image']))<meta property="og:image" content="{{ $seo['og_image'] }}">@endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="tpl-{{ $template }} fp-body">

    @if($isPreview)
        <div class="bg-amber-100 text-amber-900 text-center text-xs py-1.5 px-4">
            {{ __('frontpage.preview_draft') }}
        </div>
    @endif

    {{-- ============ Header ============ --}}
    <header class="fp-header">
        <div class="fp-container flex h-16 items-center justify-between gap-4">
            <a href="{{ url('/') }}" class="fp-brand">
                @if($mitra?->logo)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($mitra->logo) }}" alt="{{ $brandName }}" class="w-8 h-8 rounded-lg object-contain">
                @else
                    <span class="fp-brand__mark">{{ strtoupper(substr($brandName, 0, 1)) }}</span>
                @endif
                <span class="fp-brand__name">{{ $brandName }}</span>
            </a>

            <nav class="hidden items-center gap-8 lg:flex">
                @if($services)<a href="#layanan" class="fp-nav-link">{{ __('frontpage.services') }}</a>@endif
                @if($features)<a href="#fitur" class="fp-nav-link">{{ __('frontpage.features') }}</a>@endif
                @if($faqs)<a href="#faq" class="fp-nav-link">{{ __('frontpage.faq') }}</a>@endif
                @if($contact)<a href="#kontak" class="fp-nav-link">{{ __('frontpage.contact') }}</a>@endif
                @if($mapIframe)<a href="#lokasi" class="fp-nav-link">{{ __('frontpage.location') }}</a>@endif
            </nav>

            <div class="hidden items-center gap-3 lg:flex">
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="fp-btn fp-btn--primary">{{ __('common.dashboard') }}</a>
                @else
                    <a href="{{ route('login') }}" class="fp-btn fp-btn--outline">{{ __('common.login') }}</a>
                    <a href="{{ route('mitra.register') }}" class="fp-btn fp-btn--primary">{{ __('common.register') }}</a>
                @endauth
            </div>

            {{-- Menu mobile --}}
            <details class="relative lg:hidden">
                <summary class="fp-btn fp-btn--outline fp-btn--sm cursor-pointer list-none">{{ __('frontpage.menu') }}</summary>
                <nav class="absolute right-0 top-[calc(100%+0.5rem)] w-56 rounded-xl border border-slate-200 bg-white p-2 shadow-xl z-50">
                    @if($services)<a href="#layanan" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">{{ __('frontpage.services') }}</a>@endif
                    @if($features)<a href="#fitur" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">{{ __('frontpage.features') }}</a>@endif
                    @if($faqs)<a href="#faq" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">{{ __('frontpage.faq') }}</a>@endif
                    @if($contact)<a href="#kontak" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">{{ __('frontpage.contact') }}</a>@endif
                    @if($mapIframe)<a href="#lokasi" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">{{ __('frontpage.location') }}</a>@endif
                    <div class="my-2 border-t border-slate-100"></div>
                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="fp-btn fp-btn--primary mb-1 w-full">{{ __('common.dashboard') }}</a>
                    @else
                        <a href="{{ route('login') }}" class="fp-btn fp-btn--outline mb-1 w-full">{{ __('common.login') }}</a>
                        <a href="{{ route('mitra.register') }}" class="fp-btn fp-btn--primary w-full">{{ __('common.register') }}</a>
                    @endauth
                </nav>
            </details>
        </div>
    </header>

    <main>
        {{-- ============ Hero ============ --}}
        <section class="fp-container pt-12 sm:pt-16 lg:pt-20">
            <div class="fp-hero-panel {{ $heroImage ? 'has-image' : '' }} px-6 py-14 sm:px-10 sm:py-16 lg:px-16 lg:py-24"
                 @if($heroImage) style="background-image:url('{{ $heroImage }}')" @endif>
                <div class="relative z-10 max-w-3xl">
                    @if(!empty($hero['eyebrow']))
                        <p class="fp-eyebrow !text-[var(--fp-mint)]">{{ $hero['eyebrow'] }}</p>
                    @endif
                    <h1 class="mt-4 text-4xl font-semibold leading-[1.08] tracking-tight text-white sm:text-5xl lg:text-6xl">{{ $hero['title'] ?? '' }}</h1>
                    @if(!empty($hero['subtitle']))
                        <p class="mt-5 max-w-2xl text-base leading-relaxed text-white/75 sm:text-lg">{{ $hero['subtitle'] }}</p>
                    @endif
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ $heroBtnUrl }}" class="fp-btn fp-btn--primary">{{ $heroBtnText }}</a>
                        @guest
                            <a href="{{ route('mitra.register') }}" class="fp-btn fp-btn--light">{{ __('common.register') }}</a>
                        @endguest
                    </div>
                    @if($features)
                        <ul class="mt-10 grid gap-x-6 gap-y-3 text-sm text-white/70 sm:grid-cols-2">
                            @foreach(array_slice($features, 0, 4) as $f)
                                <li class="flex items-start gap-2.5">
                                    <span class="fp-dot"></span>
                                    <span>{{ $f['title'] ?? '' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </section>

        {{-- ============ Layanan ============ --}}
        @if($services)
            <section id="layanan" class="fp-section">
                <div class="fp-container">
                    <div class="fp-reveal max-w-2xl">
                        <p class="fp-eyebrow">{{ __('frontpage.platform') }}</p>
                        <h2 class="mt-3 text-3xl font-semibold tracking-tight text-[var(--fp-navy)] sm:text-4xl">{{ __('frontpage.services') }}</h2>
                        @if(!empty($hero['subtitle']))
                            <p class="mt-4 text-base leading-relaxed">{{ $hero['subtitle'] }}</p>
                        @endif
                    </div>
                    <div class="mt-10 grid gap-6 md:grid-cols-3">
                        @foreach($services as $item)
                            <article class="fp-card fp-reveal">
                                <div class="flex items-center justify-between gap-3">
                                    <h3 class="text-lg font-semibold text-[var(--fp-navy)]">{{ $item['title'] ?? '' }}</h3>
                                    @if(!empty($item['tag']))
                                        <span class="fp-badge {{ $tagClass($item['tag']) }}">{{ $item['tag'] }}</span>
                                    @endif
                                </div>
                                <p class="text-sm leading-relaxed">{{ $item['description'] ?? '' }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- ============ Fitur ============ --}}
        @if($features)
            <section id="fitur" class="fp-section border-t border-[var(--fp-line)] bg-white">
                <div class="fp-container">
                    <div class="fp-reveal max-w-2xl">
                        <p class="fp-eyebrow">{{ __('frontpage.capabilities') }}</p>
                        <h2 class="mt-3 text-3xl font-semibold tracking-tight text-[var(--fp-navy)] sm:text-4xl">{{ __('frontpage.features') }}</h2>
                    </div>
                    <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach($features as $i => $item)
                            <article class="fp-card fp-reveal">
                                <span class="fp-num">{{ $i + 1 }}</span>
                                <h3 class="text-base font-semibold text-[var(--fp-navy)]">{{ $item['title'] ?? '' }}</h3>
                                <p class="text-sm leading-relaxed">{{ $item['description'] ?? '' }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- ============ Galeri ============ --}}
        @if($media)
            <section class="fp-section">
                <div class="fp-container">
                    <div class="fp-reveal max-w-2xl">
                        <p class="fp-eyebrow">{{ __('frontpage.media') }}</p>
                        <h2 class="mt-3 text-3xl font-semibold tracking-tight text-[var(--fp-navy)] sm:text-4xl">{{ __('frontpage.gallery') }}</h2>
                    </div>
                    <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($media as $m)
                            <figure class="overflow-hidden rounded-xl border border-[var(--fp-line)] bg-white">
                                <img src="{{ $m['url'] }}" alt="{{ $m['alt'] ?? '' }}" class="h-52 w-full object-cover">
                            </figure>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- ============ FAQ ============ --}}
        @if($faqs)
            <section id="faq" class="fp-section border-t border-[var(--fp-line)] bg-white">
                <div class="fp-container">
                    <div class="fp-reveal max-w-2xl">
                        <p class="fp-eyebrow">{{ __('frontpage.questions') }}</p>
                        <h2 class="mt-3 text-3xl font-semibold tracking-tight text-[var(--fp-navy)] sm:text-4xl">{{ __('frontpage.faq') }}</h2>
                    </div>
                    <div class="mx-auto mt-10 max-w-3xl space-y-3">
                        @foreach($faqs as $item)
                            <details class="fp-faq">
                                <summary class="flex items-center justify-between gap-4">
                                    <span>{{ $item['question'] ?? '' }}</span>
                                    <span class="text-[var(--fp-mist)]">+</span>
                                </summary>
                                <p class="mt-3 text-sm leading-relaxed">{{ $item['answer'] ?? '' }}</p>
                            </details>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- ============ Kontak ============ --}}
        @if($contact)
            <section id="kontak" class="fp-section">
                <div class="fp-container">
                    <div class="fp-hero-panel px-6 py-12 sm:px-10">
                        <div class="relative z-10 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="fp-eyebrow !text-[var(--fp-mint)]">{{ __('frontpage.contact_us') }}</p>
                                <h2 class="mt-2 text-2xl font-semibold text-white sm:text-3xl">{{ __('frontpage.contact') }}</h2>
                            </div>
                            <div class="text-sm text-white/80 space-y-1">
                                @if(!empty($contact['email']))<div>{{ __('common.email') }}: <a class="underline" href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a></div>@endif
                                @if(!empty($contact['phone']))<div>{{ __('common.phone') }}: {{ $contact['phone'] }}</div>@endif
                                @if(!empty($contact['address']))<div>{{ $contact['address'] }}</div>@endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- ============ Lokasi (Google Maps) ============ --}}
        @if($mapIframe)
            <section id="lokasi" class="fp-section border-t border-[var(--fp-line)] bg-white">
                <div class="fp-container">
                    <div class="fp-reveal max-w-2xl">
                        <p class="fp-eyebrow">{{ __('frontpage.location') }}</p>
                        <h2 class="mt-3 text-3xl font-semibold tracking-tight text-[var(--fp-navy)] sm:text-4xl">{{ __('frontpage.map_title') }}</h2>
                        @if(!empty($contact['address']))
                            <p class="mt-4 text-base leading-relaxed">{{ $contact['address'] }}</p>
                        @endif
                    </div>
                    <div class="fp-map fp-reveal mt-8">
                        {!! $mapIframe !!}
                    </div>
                </div>
            </section>
        @endif
    </main>

    <footer class="border-t border-[var(--fp-line)] bg-white">
        <div class="fp-container flex flex-col items-center justify-between gap-3 py-8 text-xs text-[var(--fp-mist)] sm:flex-row">
            <span>&copy; {{ date('Y') }} {{ $brandName }}. {{ __('frontpage.all_rights') }}</span>
            <span>{{ config('app.name') }}</span>
        </div>
    </footer>
</body>
</html>

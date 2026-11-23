@extends('layouts.admin')

@section('title', $title)

@section('content')
    @php
        $localesData = $website->content['locales'] ?? [];
        $dataOf = fn (string $code) => array_replace_recursive(\Modules\Website\Services\WebsiteService::blankLocale(), $localesData[$code] ?? []);
        $mediaRows = $website->media();
        $field = 'block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:border-[var(--color-primary)]';
    @endphp

    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-4" id="wt-form">
        @csrf

        <div class="card">
            <div class="card-header flex flex-wrap items-center gap-2">
                <span>{{ $title }}</span>
                <span class="badge {{ $website->isPublished() ? 'badge-success' : 'badge-warning' }}">
                    {{ $website->isPublished() ? __('website.published') : __('website.draft') }}
                </span>
                <div class="ml-auto flex flex-wrap items-center gap-2">
                    <a href="{{ $previewUrl }}" target="_blank" rel="noopener" class="btn btn-secondary">
                        <i data-lucide="external-link"></i> {{ __('website.preview') }}
                    </a>
                    <button type="submit" name="status" value="draft" class="btn btn-secondary">
                        <i data-lucide="save"></i> {{ __('website.save_draft') }}
                    </button>
                    <button type="submit" name="status" value="published" class="btn btn-primary">
                        <i data-lucide="upload-cloud"></i> {{ __('website.publish') }}
                    </button>
                </div>
            </div>

            <div class="card-body space-y-6">
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="form-label">{{ __('website.website_name') }}</label>
                        <input type="text" value="{{ $website->name }}" class="form-control" disabled>
                    </div>
                    <div>
                        <label class="form-label">{{ __('website.template') }}</label>
                        <select name="template" class="form-control js-select2">
                            @foreach($templates as $key => $label)
                                <option value="{{ $key }}" @selected(($website->template ?: 'default') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">{{ __('website.address') }}</label>
                        <input type="text" value="{{ $mitra ? url('/' . $mitra->slug) : url('/') }}" class="form-control" disabled>
                    </div>
                </div>

                {{-- Tab bahasa --}}
                <div>
                    <div class="flex gap-1 border-b border-slate-200">
                        @foreach($locales as $code => $loc)
                            <button type="button" class="wt-tab {{ $loop->first ? 'active' : '' }}" data-wt-tab="{{ $code }}">
                                {{ $loc['flag'] }}
                            </button>
                        @endforeach
                    </div>

                    @foreach($locales as $code => $loc)
                        @php $d = $dataOf($code); @endphp
                        <div class="wt-panel {{ $loop->first ? 'active' : '' }} pt-5 space-y-6" data-wt-panel="{{ $code }}">

                            {{-- Hero --}}
                            <section>
                                <h3 class="text-sm font-semibold text-slate-700 mb-3">{{ __('website.hero') }}</h3>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <div class="sm:col-span-2">
                                        <label class="form-label">{{ __('website.judul') }}</label>
                                        <input type="text" name="content[{{ $code }}][hero][title]" value="{{ $d['hero']['title'] }}" class="{{ $field }}">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="form-label">{{ __('website.subjudul') }}</label>
                                        <textarea name="content[{{ $code }}][hero][subtitle]" rows="2" class="{{ $field }}">{{ $d['hero']['subtitle'] }}</textarea>
                                    </div>
                                    <div>
                                        <label class="form-label">{{ __('website.button_text') }}</label>
                                        <input type="text" name="content[{{ $code }}][hero][button_text]" value="{{ $d['hero']['button_text'] }}" class="{{ $field }}">
                                    </div>
                                    <div>
                                        <label class="form-label">{{ __('website.button_url') }}</label>
                                        <input type="text" name="content[{{ $code }}][hero][button_url]" value="{{ $d['hero']['button_url'] }}" class="{{ $field }}">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="form-label">{{ __('website.hero_image_url') }}</label>
                                        <input type="text" name="content[{{ $code }}][hero][image]" value="{{ $d['hero']['image'] }}" placeholder="https://..." class="{{ $field }}">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="form-label">{{ __('website.hero_image_upload') }}</label>
                                        <input type="file" name="content[{{ $code }}][hero][image_file]" accept="image/*" class="{{ $field }}">
                                        @if(! empty($d['hero']['image']))
                                            <img src="{{ $d['hero']['image'] }}" alt="" class="mt-2 h-24 rounded-lg object-cover border border-slate-200">
                                        @endif
                                        <p class="text-xs text-slate-400 mt-1">{{ __('website.hero_image_hint') }}</p>
                                    </div>
                                </div>
                            </section>

                            {{-- Layanan --}}
                            <section>
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-sm font-semibold text-slate-700">{{ __('website.services') }}</h3>
                                    <button type="button" class="btn btn-secondary btn-sm" data-repeat-add="rp-service-{{ $code }}">
                                        <i data-lucide="plus"></i> {{ __('website.add_service') }}
                                    </button>
                                </div>
                                <div id="rp-service-{{ $code }}" class="space-y-3">
                                    <div data-repeat-rows class="space-y-3">
                                        @foreach($d['services'] as $i => $row)
                                            <div class="wt-row grid gap-2 sm:grid-cols-12 items-start bg-slate-50 border border-slate-100 rounded-lg p-3" data-repeat-row>
                                                <div class="sm:col-span-4">
                                                    <input type="text" name="content[{{ $code }}][services][{{ $i }}][title]" value="{{ $row['title'] ?? '' }}" placeholder="{{ __('website.placeholder_title') }}" class="{{ $field }}">
                                                </div>
                                                <div class="sm:col-span-2">
                                                    <input type="text" name="content[{{ $code }}][services][{{ $i }}][tag]" value="{{ $row['tag'] ?? '' }}" placeholder="{{ __('website.placeholder_tag') }}" class="{{ $field }}">
                                                </div>
                                                <div class="sm:col-span-5">
                                                    <input type="text" name="content[{{ $code }}][services][{{ $i }}][description]" value="{{ $row['description'] ?? '' }}" placeholder="{{ __('website.placeholder_description') }}" class="{{ $field }}">
                                                </div>
                                                <div class="sm:col-span-1 text-right">
                                                    <button type="button" class="btn btn-secondary btn-sm text-red-600" data-repeat-remove><i data-lucide="trash-2"></i></button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <template data-row-template>
                                        <div class="wt-row grid gap-2 sm:grid-cols-12 items-start bg-slate-50 border border-slate-100 rounded-lg p-3" data-repeat-row>
                                            <div class="sm:col-span-4"><input type="text" name="content[{{ $code }}][services][__INDEX__][title]" placeholder="{{ __('website.placeholder_title') }}" class="{{ $field }}"></div>
                                            <div class="sm:col-span-2"><input type="text" name="content[{{ $code }}][services][__INDEX__][tag]" placeholder="{{ __('website.placeholder_tag') }}" class="{{ $field }}"></div>
                                            <div class="sm:col-span-5"><input type="text" name="content[{{ $code }}][services][__INDEX__][description]" placeholder="{{ __('website.placeholder_description') }}" class="{{ $field }}"></div>
                                            <div class="sm:col-span-1 text-right"><button type="button" class="btn btn-secondary btn-sm text-red-600" data-repeat-remove><i data-lucide="trash-2"></i></button></div>
                                        </div>
                                    </template>
                                </div>
                            </section>

                            {{-- Fitur --}}
                            <section>
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-sm font-semibold text-slate-700">{{ __('website.features') }}</h3>
                                    <button type="button" class="btn btn-secondary btn-sm" data-repeat-add="rp-feature-{{ $code }}">
                                        <i data-lucide="plus"></i> {{ __('website.add_feature') }}
                                    </button>
                                </div>
                                <div id="rp-feature-{{ $code }}" class="space-y-3">
                                    <div data-repeat-rows class="space-y-3">
                                        @foreach($d['features'] as $i => $row)
                                            <div class="wt-row grid gap-2 sm:grid-cols-12 items-start bg-slate-50 border border-slate-100 rounded-lg p-3" data-repeat-row>
                                                <div class="sm:col-span-4">
                                                    <input type="text" name="content[{{ $code }}][features][{{ $i }}][title]" value="{{ $row['title'] ?? '' }}" placeholder="{{ __('website.placeholder_title') }}" class="{{ $field }}">
                                                </div>
                                                <div class="sm:col-span-7">
                                                    <input type="text" name="content[{{ $code }}][features][{{ $i }}][description]" value="{{ $row['description'] ?? '' }}" placeholder="{{ __('website.placeholder_description') }}" class="{{ $field }}">
                                                </div>
                                                <div class="sm:col-span-1 text-right">
                                                    <button type="button" class="btn btn-secondary btn-sm text-red-600" data-repeat-remove><i data-lucide="trash-2"></i></button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <template data-row-template>
                                        <div class="wt-row grid gap-2 sm:grid-cols-12 items-start bg-slate-50 border border-slate-100 rounded-lg p-3" data-repeat-row>
                                            <div class="sm:col-span-4"><input type="text" name="content[{{ $code }}][features][__INDEX__][title]" placeholder="{{ __('website.placeholder_title') }}" class="{{ $field }}"></div>
                                            <div class="sm:col-span-7"><input type="text" name="content[{{ $code }}][features][__INDEX__][description]" placeholder="{{ __('website.placeholder_description') }}" class="{{ $field }}"></div>
                                            <div class="sm:col-span-1 text-right"><button type="button" class="btn btn-secondary btn-sm text-red-600" data-repeat-remove><i data-lucide="trash-2"></i></button></div>
                                        </div>
                                    </template>
                                </div>
                            </section>

                            {{-- FAQ --}}
                            <section>
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-sm font-semibold text-slate-700">{{ __('website.faq') }}</h3>
                                    <button type="button" class="btn btn-secondary btn-sm" data-repeat-add="rp-faq-{{ $code }}">
                                        <i data-lucide="plus"></i> {{ __('website.add_faq') }}
                                    </button>
                                </div>
                                <div id="rp-faq-{{ $code }}" class="space-y-3">
                                    <div data-repeat-rows class="space-y-3">
                                        @foreach($d['faq'] as $i => $row)
                                            <div class="wt-row grid gap-2 sm:grid-cols-12 items-start bg-slate-50 border border-slate-100 rounded-lg p-3" data-repeat-row>
                                                <div class="sm:col-span-4">
                                                    <input type="text" name="content[{{ $code }}][faq][{{ $i }}][question]" value="{{ $row['question'] ?? '' }}" placeholder="{{ __('website.placeholder_question') }}" class="{{ $field }}">
                                                </div>
                                                <div class="sm:col-span-7">
                                                    <input type="text" name="content[{{ $code }}][faq][{{ $i }}][answer]" value="{{ $row['answer'] ?? '' }}" placeholder="{{ __('website.placeholder_answer') }}" class="{{ $field }}">
                                                </div>
                                                <div class="sm:col-span-1 text-right">
                                                    <button type="button" class="btn btn-secondary btn-sm text-red-600" data-repeat-remove><i data-lucide="trash-2"></i></button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <template data-row-template>
                                        <div class="wt-row grid gap-2 sm:grid-cols-12 items-start bg-slate-50 border border-slate-100 rounded-lg p-3" data-repeat-row>
                                            <div class="sm:col-span-4"><input type="text" name="content[{{ $code }}][faq][__INDEX__][question]" placeholder="{{ __('website.placeholder_question') }}" class="{{ $field }}"></div>
                                            <div class="sm:col-span-7"><input type="text" name="content[{{ $code }}][faq][__INDEX__][answer]" placeholder="{{ __('website.placeholder_answer') }}" class="{{ $field }}"></div>
                                            <div class="sm:col-span-1 text-right"><button type="button" class="btn btn-secondary btn-sm text-red-600" data-repeat-remove><i data-lucide="trash-2"></i></button></div>
                                        </div>
                                    </template>
                                </div>
                            </section>

                            {{-- Kontak --}}
                            <section>
                                <h3 class="text-sm font-semibold text-slate-700 mb-3">{{ __('website.contact') }}</h3>
                                <div class="grid gap-3 sm:grid-cols-3">
                                    <div>
                                        <label class="form-label">{{ __('website.email') }}</label>
                                        <input type="text" name="content[{{ $code }}][contact][email]" value="{{ $d['contact']['email'] }}" class="{{ $field }}">
                                    </div>
                                    <div>
                                        <label class="form-label">{{ __('website.phone') }}</label>
                                        <input type="text" name="content[{{ $code }}][contact][phone]" value="{{ $d['contact']['phone'] }}" class="{{ $field }}">
                                    </div>
                                    <div>
                                        <label class="form-label">{{ __('website.address_field') }}</label>
                                        <input type="text" name="content[{{ $code }}][contact][address]" value="{{ $d['contact']['address'] }}" class="{{ $field }}">
                                    </div>
                                    <div class="sm:col-span-3">
                                        <label class="form-label">{{ __('website.map_embed') }}</label>
                                        <textarea name="content[{{ $code }}][contact][map_embed]" rows="3" placeholder="{{ __('website.map_embed_placeholder') }}" class="{{ $field }}">{{ $d['contact']['map_embed'] ?? '' }}</textarea>
                                        <p class="text-xs text-slate-400 mt-1">{{ __('website.map_embed_hint') }}</p>
                                    </div>
                                </div>
                            </section>

                            {{-- SEO --}}
                            <section>
                                <h3 class="text-sm font-semibold text-slate-700 mb-3">{{ __('website.seo') }}</h3>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <div>
                                        <label class="form-label">{{ __('website.seo_title') }}</label>
                                        <input type="text" name="content[{{ $code }}][seo][title]" value="{{ $d['seo']['title'] }}" class="{{ $field }}">
                                    </div>
                                    <div>
                                        <label class="form-label">{{ __('website.og_image') }}</label>
                                        <input type="text" name="content[{{ $code }}][seo][og_image]" value="{{ $d['seo']['og_image'] }}" class="{{ $field }}">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="form-label">{{ __('website.seo_description') }}</label>
                                        <textarea name="content[{{ $code }}][seo][description]" rows="2" class="{{ $field }}">{{ $d['seo']['description'] }}</textarea>
                                    </div>
                                </div>
                            </section>
                        </div>
                    @endforeach
                </div>

                {{-- Media (bersama) --}}
                <section class="border-t border-slate-100 pt-5">
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="text-sm font-semibold text-slate-700">{{ __('website.media') }}</h3>
                        <button type="button" class="btn btn-secondary btn-sm" data-repeat-add="rp-media">
                            <i data-lucide="plus"></i> {{ __('website.add_media') }}
                        </button>
                    </div>
                    <p class="text-xs text-slate-400 mb-3">{{ __('website.media_hint') }}</p>
                    <div id="rp-media" class="space-y-3">
                        <div data-repeat-rows class="space-y-3">
                            @foreach($mediaRows as $i => $row)
                                <div class="wt-row grid gap-2 sm:grid-cols-12 items-start bg-slate-50 border border-slate-100 rounded-lg p-3" data-repeat-row>
                                    <div class="sm:col-span-5">
                                        <input type="text" name="media[{{ $i }}][url]" value="{{ $row['url'] ?? '' }}" placeholder="{{ __('website.image_url') }}" class="{{ $field }}">
                                    </div>
                                    <div class="sm:col-span-4">
                                        <input type="file" name="media[{{ $i }}][file]" accept="image/*" class="{{ $field }}">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <input type="text" name="media[{{ $i }}][alt]" value="{{ $row['alt'] ?? '' }}" placeholder="{{ __('website.alt_text') }}" class="{{ $field }}">
                                    </div>
                                    <div class="sm:col-span-1 text-right">
                                        <button type="button" class="btn btn-secondary btn-sm text-red-600" data-repeat-remove><i data-lucide="trash-2"></i></button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <template data-row-template>
                            <div class="wt-row grid gap-2 sm:grid-cols-12 items-start bg-slate-50 border border-slate-100 rounded-lg p-3" data-repeat-row>
                                <div class="sm:col-span-5"><input type="text" name="media[__INDEX__][url]" placeholder="{{ __('website.image_url') }}" class="{{ $field }}"></div>
                                <div class="sm:col-span-4"><input type="file" name="media[__INDEX__][file]" accept="image/*" class="{{ $field }}"></div>
                                <div class="sm:col-span-2"><input type="text" name="media[__INDEX__][alt]" placeholder="{{ __('website.alt_text') }}" class="{{ $field }}"></div>
                                <div class="sm:col-span-1 text-right"><button type="button" class="btn btn-secondary btn-sm text-red-600" data-repeat-remove><i data-lucide="trash-2"></i></button></div>
                            </div>
                        </template>
                    </div>
                </section>
            </div>
        </div>
    </form>
@endsection

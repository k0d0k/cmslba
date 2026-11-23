@extends('layouts.admin')

@section('title', $title)

@section('content')
    @php
        $localesData = $website->content['locales'] ?? [];
        $dataOf = fn (string $code) => array_replace_recursive(\App\Services\WebsiteService::blankLocale(), $localesData[$code] ?? []);
        $mediaRows = $website->media();
        $field = 'block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:border-[var(--color-primary)]';
    @endphp

    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-4" id="wt-form">
        @csrf

        <div class="card">
            <div class="card-header flex flex-wrap items-center gap-2">
                <span>{{ $title }}</span>
                <span class="badge {{ $website->isPublished() ? 'badge-success' : 'badge-warning' }}">
                    {{ $website->isPublished() ? 'Dipublikasikan' : 'Draf' }}
                </span>
                <div class="ml-auto flex flex-wrap items-center gap-2">
                    <a href="{{ $previewUrl }}" target="_blank" rel="noopener" class="btn btn-secondary">
                        <i data-lucide="external-link"></i> Pratinjau
                    </a>
                    <button type="submit" name="status" value="draft" class="btn btn-secondary">
                        <i data-lucide="save"></i> Simpan Draf
                    </button>
                    <button type="submit" name="status" value="published" class="btn btn-primary">
                        <i data-lucide="upload-cloud"></i> Publikasikan
                    </button>
                </div>
            </div>

            <div class="card-body space-y-6">
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="form-label">Nama Website</label>
                        <input type="text" value="{{ $website->name }}" class="form-control" disabled>
                    </div>
                    <div>
                        <label class="form-label">Template</label>
                        <select name="template" class="form-control js-select2">
                            @foreach($templates as $key => $label)
                                <option value="{{ $key }}" @selected(($website->template ?: 'default') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Alamat</label>
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
                                <h3 class="text-sm font-semibold text-slate-700 mb-3">Hero</h3>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <div class="sm:col-span-2">
                                        <label class="form-label">Judul</label>
                                        <input type="text" name="content[{{ $code }}][hero][title]" value="{{ $d['hero']['title'] }}" class="{{ $field }}">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="form-label">Subjudul</label>
                                        <textarea name="content[{{ $code }}][hero][subtitle]" rows="2" class="{{ $field }}">{{ $d['hero']['subtitle'] }}</textarea>
                                    </div>
                                    <div>
                                        <label class="form-label">Teks tombol</label>
                                        <input type="text" name="content[{{ $code }}][hero][button_text]" value="{{ $d['hero']['button_text'] }}" class="{{ $field }}">
                                    </div>
                                    <div>
                                        <label class="form-label">URL tombol</label>
                                        <input type="text" name="content[{{ $code }}][hero][button_url]" value="{{ $d['hero']['button_url'] }}" class="{{ $field }}">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="form-label">Gambar hero (URL)</label>
                                        <input type="text" name="content[{{ $code }}][hero][image]" value="{{ $d['hero']['image'] }}" placeholder="https://..." class="{{ $field }}">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="form-label">Atau unggah gambar hero</label>
                                        <input type="file" name="content[{{ $code }}][hero][image_file]" accept="image/*" class="{{ $field }}">
                                        @if(! empty($d['hero']['image']))
                                            <img src="{{ $d['hero']['image'] }}" alt="" class="mt-2 h-24 rounded-lg object-cover border border-slate-200">
                                        @endif
                                        <p class="text-xs text-slate-400 mt-1">Kosongkan URL di atas bila tidak memakai gambar. Mengunggah berkas akan menggantikan URL.</p>
                                    </div>
                                </div>
                            </section>

                            {{-- Layanan --}}
                            <section>
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-sm font-semibold text-slate-700">Layanan</h3>
                                    <button type="button" class="btn btn-secondary btn-sm" data-repeat-add="rp-service-{{ $code }}">
                                        <i data-lucide="plus"></i> Tambah layanan
                                    </button>
                                </div>
                                <div id="rp-service-{{ $code }}" class="space-y-3">
                                    <div data-repeat-rows class="space-y-3">
                                        @foreach($d['services'] as $i => $row)
                                            <div class="wt-row grid gap-2 sm:grid-cols-12 items-start bg-slate-50 border border-slate-100 rounded-lg p-3" data-repeat-row>
                                                <div class="sm:col-span-4">
                                                    <input type="text" name="content[{{ $code }}][services][{{ $i }}][title]" value="{{ $row['title'] ?? '' }}" placeholder="Judul" class="{{ $field }}">
                                                </div>
                                                <div class="sm:col-span-2">
                                                    <input type="text" name="content[{{ $code }}][services][{{ $i }}][tag]" value="{{ $row['tag'] ?? '' }}" placeholder="Label (ops.)" class="{{ $field }}">
                                                </div>
                                                <div class="sm:col-span-5">
                                                    <input type="text" name="content[{{ $code }}][services][{{ $i }}][description]" value="{{ $row['description'] ?? '' }}" placeholder="Deskripsi" class="{{ $field }}">
                                                </div>
                                                <div class="sm:col-span-1 text-right">
                                                    <button type="button" class="btn btn-secondary btn-sm text-red-600" data-repeat-remove><i data-lucide="trash-2"></i></button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <template data-row-template>
                                        <div class="wt-row grid gap-2 sm:grid-cols-12 items-start bg-slate-50 border border-slate-100 rounded-lg p-3" data-repeat-row>
                                            <div class="sm:col-span-4"><input type="text" name="content[{{ $code }}][services][__INDEX__][title]" placeholder="Judul" class="{{ $field }}"></div>
                                            <div class="sm:col-span-2"><input type="text" name="content[{{ $code }}][services][__INDEX__][tag]" placeholder="Label (ops.)" class="{{ $field }}"></div>
                                            <div class="sm:col-span-5"><input type="text" name="content[{{ $code }}][services][__INDEX__][description]" placeholder="Deskripsi" class="{{ $field }}"></div>
                                            <div class="sm:col-span-1 text-right"><button type="button" class="btn btn-secondary btn-sm text-red-600" data-repeat-remove><i data-lucide="trash-2"></i></button></div>
                                        </div>
                                    </template>
                                </div>
                            </section>

                            {{-- Fitur --}}
                            <section>
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-sm font-semibold text-slate-700">Fitur</h3>
                                    <button type="button" class="btn btn-secondary btn-sm" data-repeat-add="rp-feature-{{ $code }}">
                                        <i data-lucide="plus"></i> Tambah fitur
                                    </button>
                                </div>
                                <div id="rp-feature-{{ $code }}" class="space-y-3">
                                    <div data-repeat-rows class="space-y-3">
                                        @foreach($d['features'] as $i => $row)
                                            <div class="wt-row grid gap-2 sm:grid-cols-12 items-start bg-slate-50 border border-slate-100 rounded-lg p-3" data-repeat-row>
                                                <div class="sm:col-span-4">
                                                    <input type="text" name="content[{{ $code }}][features][{{ $i }}][title]" value="{{ $row['title'] ?? '' }}" placeholder="Judul" class="{{ $field }}">
                                                </div>
                                                <div class="sm:col-span-7">
                                                    <input type="text" name="content[{{ $code }}][features][{{ $i }}][description]" value="{{ $row['description'] ?? '' }}" placeholder="Deskripsi" class="{{ $field }}">
                                                </div>
                                                <div class="sm:col-span-1 text-right">
                                                    <button type="button" class="btn btn-secondary btn-sm text-red-600" data-repeat-remove><i data-lucide="trash-2"></i></button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <template data-row-template>
                                        <div class="wt-row grid gap-2 sm:grid-cols-12 items-start bg-slate-50 border border-slate-100 rounded-lg p-3" data-repeat-row>
                                            <div class="sm:col-span-4"><input type="text" name="content[{{ $code }}][features][__INDEX__][title]" placeholder="Judul" class="{{ $field }}"></div>
                                            <div class="sm:col-span-7"><input type="text" name="content[{{ $code }}][features][__INDEX__][description]" placeholder="Deskripsi" class="{{ $field }}"></div>
                                            <div class="sm:col-span-1 text-right"><button type="button" class="btn btn-secondary btn-sm text-red-600" data-repeat-remove><i data-lucide="trash-2"></i></button></div>
                                        </div>
                                    </template>
                                </div>
                            </section>

                            {{-- FAQ --}}
                            <section>
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-sm font-semibold text-slate-700">FAQ</h3>
                                    <button type="button" class="btn btn-secondary btn-sm" data-repeat-add="rp-faq-{{ $code }}">
                                        <i data-lucide="plus"></i> Tambah FAQ
                                    </button>
                                </div>
                                <div id="rp-faq-{{ $code }}" class="space-y-3">
                                    <div data-repeat-rows class="space-y-3">
                                        @foreach($d['faq'] as $i => $row)
                                            <div class="wt-row grid gap-2 sm:grid-cols-12 items-start bg-slate-50 border border-slate-100 rounded-lg p-3" data-repeat-row>
                                                <div class="sm:col-span-4">
                                                    <input type="text" name="content[{{ $code }}][faq][{{ $i }}][question]" value="{{ $row['question'] ?? '' }}" placeholder="Pertanyaan" class="{{ $field }}">
                                                </div>
                                                <div class="sm:col-span-7">
                                                    <input type="text" name="content[{{ $code }}][faq][{{ $i }}][answer]" value="{{ $row['answer'] ?? '' }}" placeholder="Jawaban" class="{{ $field }}">
                                                </div>
                                                <div class="sm:col-span-1 text-right">
                                                    <button type="button" class="btn btn-secondary btn-sm text-red-600" data-repeat-remove><i data-lucide="trash-2"></i></button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <template data-row-template>
                                        <div class="wt-row grid gap-2 sm:grid-cols-12 items-start bg-slate-50 border border-slate-100 rounded-lg p-3" data-repeat-row>
                                            <div class="sm:col-span-4"><input type="text" name="content[{{ $code }}][faq][__INDEX__][question]" placeholder="Pertanyaan" class="{{ $field }}"></div>
                                            <div class="sm:col-span-7"><input type="text" name="content[{{ $code }}][faq][__INDEX__][answer]" placeholder="Jawaban" class="{{ $field }}"></div>
                                            <div class="sm:col-span-1 text-right"><button type="button" class="btn btn-secondary btn-sm text-red-600" data-repeat-remove><i data-lucide="trash-2"></i></button></div>
                                        </div>
                                    </template>
                                </div>
                            </section>

                            {{-- Kontak --}}
                            <section>
                                <h3 class="text-sm font-semibold text-slate-700 mb-3">Kontak</h3>
                                <div class="grid gap-3 sm:grid-cols-3">
                                    <div>
                                        <label class="form-label">Email</label>
                                        <input type="text" name="content[{{ $code }}][contact][email]" value="{{ $d['contact']['email'] }}" class="{{ $field }}">
                                    </div>
                                    <div>
                                        <label class="form-label">Telepon</label>
                                        <input type="text" name="content[{{ $code }}][contact][phone]" value="{{ $d['contact']['phone'] }}" class="{{ $field }}">
                                    </div>
                                    <div>
                                        <label class="form-label">Alamat</label>
                                        <input type="text" name="content[{{ $code }}][contact][address]" value="{{ $d['contact']['address'] }}" class="{{ $field }}">
                                    </div>
                                </div>
                            </section>

                            {{-- SEO --}}
                            <section>
                                <h3 class="text-sm font-semibold text-slate-700 mb-3">SEO</h3>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <div>
                                        <label class="form-label">Judul SEO</label>
                                        <input type="text" name="content[{{ $code }}][seo][title]" value="{{ $d['seo']['title'] }}" class="{{ $field }}">
                                    </div>
                                    <div>
                                        <label class="form-label">Gambar Open Graph (URL)</label>
                                        <input type="text" name="content[{{ $code }}][seo][og_image]" value="{{ $d['seo']['og_image'] }}" class="{{ $field }}">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="form-label">Deskripsi SEO</label>
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
                        <h3 class="text-sm font-semibold text-slate-700">Media</h3>
                        <button type="button" class="btn btn-secondary btn-sm" data-repeat-add="rp-media">
                            <i data-lucide="plus"></i> Tambah media
                        </button>
                    </div>
                    <p class="text-xs text-slate-400 mb-3">Unggah berkas (JPG, PNG, WEBP, GIF, SVG, maks 4 MB) atau isi URL gambar. Teks alternatif bersifat opsional.</p>
                    <div id="rp-media" class="space-y-3">
                        <div data-repeat-rows class="space-y-3">
                            @foreach($mediaRows as $i => $row)
                                <div class="wt-row grid gap-2 sm:grid-cols-12 items-start bg-slate-50 border border-slate-100 rounded-lg p-3" data-repeat-row>
                                    <div class="sm:col-span-5">
                                        <input type="text" name="media[{{ $i }}][url]" value="{{ $row['url'] ?? '' }}" placeholder="URL gambar" class="{{ $field }}">
                                    </div>
                                    <div class="sm:col-span-4">
                                        <input type="file" name="media[{{ $i }}][file]" accept="image/*" class="{{ $field }}">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <input type="text" name="media[{{ $i }}][alt]" value="{{ $row['alt'] ?? '' }}" placeholder="Teks alternatif" class="{{ $field }}">
                                    </div>
                                    <div class="sm:col-span-1 text-right">
                                        <button type="button" class="btn btn-secondary btn-sm text-red-600" data-repeat-remove><i data-lucide="trash-2"></i></button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <template data-row-template>
                            <div class="wt-row grid gap-2 sm:grid-cols-12 items-start bg-slate-50 border border-slate-100 rounded-lg p-3" data-repeat-row>
                                <div class="sm:col-span-5"><input type="text" name="media[__INDEX__][url]" placeholder="URL gambar" class="{{ $field }}"></div>
                                <div class="sm:col-span-4"><input type="file" name="media[__INDEX__][file]" accept="image/*" class="{{ $field }}"></div>
                                <div class="sm:col-span-2"><input type="text" name="media[__INDEX__][alt]" placeholder="Teks alternatif" class="{{ $field }}"></div>
                                <div class="sm:col-span-1 text-right"><button type="button" class="btn btn-secondary btn-sm text-red-600" data-repeat-remove><i data-lucide="trash-2"></i></button></div>
                            </div>
                        </template>
                    </div>
                </section>
            </div>
        </div>
    </form>
@endsection

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mitra;
use App\Models\Website;
use App\Services\WebsiteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Pengelolaan website/frontpage:
 *  - Platform (Principal) : route admin.website.*
 *  - Mitra aktif          : route admin.mitra_website.*
 */
class WebsiteController extends Controller
{
    /* ============================ Platform ============================ */

    public function platform()
    {
        abort_unless(auth()->user()?->isPrincipal(), 403);

        return $this->editor($this->websiteFor(null), null);
    }

    public function savePlatform(Request $request)
    {
        abort_unless(auth()->user()?->isPrincipal(), 403);

        return $this->save($request, null, 'admin.website.index');
    }

    /* ============================== Mitra ============================= */

    public function mitra()
    {
        $mitra = $this->currentMitra();

        if (! $mitra) {
            return redirect()->route('admin.dashboard')->with('error', __('website.no_mitra'));
        }

        return $this->editor($this->websiteFor($mitra), $mitra);
    }

    public function saveMitra(Request $request)
    {
        $mitra = $this->currentMitra();

        if (! $mitra) {
            return redirect()->route('admin.dashboard')->with('error', __('website.no_mitra'));
        }

        return $this->save($request, $mitra, 'admin.mitra_website.index');
    }

    /* ============================= Bersama ============================ */

    protected function editor(Website $website, ?Mitra $mitra)
    {
        return view('admin.website.edit', [
            'website'    => $website,
            'mitra'      => $mitra,
            'title'      => $mitra ? __('website.title_mitra') : __('website.title_platform'),
            'action'     => $mitra ? route('admin.mitra_website.save') : route('admin.website.save'),
            'locales'    => WebsiteService::locales(),
            'templates'  => WebsiteService::templates(),
            'previewUrl' => $mitra ? route('frontpage.mitra', $mitra->slug) : route('home'),
        ]);
    }

    protected function save(Request $request, ?Mitra $mitra, string $redirectRoute)
    {
        $request->validate([
            'template' => 'nullable|string|max:50',
        ]);

        $website = $this->websiteFor($mitra);
        $status = $request->input('status') === 'published' ? 'published' : 'draft';

        $website->fill([
            'template' => $request->input('template', 'default') ?: 'default',
            'status'   => $status,
            'content'  => $this->buildContent($request),
            'published_at' => $status === 'published' ? ($website->published_at ?? now()) : $website->published_at,
        ])->save();

        return redirect()->route($redirectRoute)->with(
            'success',
            $status === 'published' ? __('website.saved_published') : __('website.saved_draft')
        );
    }

    /** Website platform (mitra_id null) atau website milik mitra. */
    protected function websiteFor(?Mitra $mitra): Website
    {
        if ($mitra) {
            return Website::firstOrCreate(
                ['mitra_id' => $mitra->id],
                [
                    'name'     => $mitra->name,
                    'slug'     => 'mitra-' . $mitra->slug,
                    'template' => 'default',
                    'status'   => 'draft',
                    'content'  => WebsiteService::defaultContent(),
                ]
            );
        }

        return Website::firstOrCreate(
            ['slug' => 'platform'],
            [
                'mitra_id' => null,
                'name'     => config('app.name'),
                'template' => 'default',
                'status'   => 'draft',
                'content'  => WebsiteService::defaultContent(),
            ]
        );
    }

    protected function currentMitra(): ?Mitra
    {
        if (app()->bound('active_mitra')) {
            return app('active_mitra');
        }

        $user = auth()->user();

        return $user?->mitra_id ? Mitra::find($user->mitra_id) : null;
    }

    /** Susun konten (multi-bahasa + media) dari input form. */
    protected function buildContent(Request $request): array
    {
        $locales = [];

        foreach (array_keys(WebsiteService::locales()) as $code) {
            $in = $request->input("content.$code", []);

            // Gambar hero: utamakan berkas terunggah, jika tidak pakai URL teks.
            $heroImage = (string) ($in['hero']['image'] ?? '');
            if ($file = $request->file("content.$code.hero.image_file")) {
                $heroImage = Storage::url($file->store('uploads/website', 'public'));
            }

            $locales[$code] = [
                'hero' => [
                    'eyebrow'     => (string) ($in['hero']['eyebrow'] ?? ''),
                    'title'       => (string) ($in['hero']['title'] ?? ''),
                    'subtitle'    => (string) ($in['hero']['subtitle'] ?? ''),
                    'button_text' => (string) ($in['hero']['button_text'] ?? ''),
                    'button_url'  => (string) ($in['hero']['button_url'] ?? ''),
                    'image'       => $heroImage,
                ],
                'services' => $this->cleanRows($in['services'] ?? [], ['title', 'description', 'tag']),
                'features' => $this->cleanRows($in['features'] ?? [], ['title', 'description']),
                'faq'      => $this->cleanRows($in['faq'] ?? [], ['question', 'answer']),
                'contact'  => [
                    'email'     => (string) ($in['contact']['email'] ?? ''),
                    'phone'     => (string) ($in['contact']['phone'] ?? ''),
                    'address'   => (string) ($in['contact']['address'] ?? ''),
                    'map_embed' => (string) ($in['contact']['map_embed'] ?? ''),
                ],
                'seo' => [
                    'title'       => (string) ($in['seo']['title'] ?? ''),
                    'description' => (string) ($in['seo']['description'] ?? ''),
                    'og_image'    => (string) ($in['seo']['og_image'] ?? ''),
                ],
            ];
        }

        return [
            'locales' => $locales,
            'media'   => $this->buildMedia($request),
        ];
    }

    /** Baris berulang (layanan/fitur/faq): buang baris kosong, rapikan index. */
    protected function cleanRows($rows, array $keys): array
    {
        $out = [];

        foreach (is_array($rows) ? $rows : [] as $row) {
            if (! is_array($row)) {
                continue;
            }
            $clean = [];
            foreach ($keys as $key) {
                $clean[$key] = (string) ($row[$key] ?? '');
            }
            if (implode('', $clean) !== '') {
                $out[] = $clean;
            }
        }

        return $out;
    }

    /** Media bersama: URL teks atau berkas terunggah. */
    protected function buildMedia(Request $request): array
    {
        $out = [];

        foreach ((array) $request->input('media', []) as $i => $row) {
            $url = trim((string) ($row['url'] ?? ''));

            if ($file = $request->file("media.$i.file")) {
                $url = Storage::url($file->store('uploads/website', 'public'));
            }

            if ($url === '') {
                continue;
            }

            $out[] = ['url' => $url, 'alt' => trim((string) ($row['alt'] ?? ''))];
        }

        return $out;
    }
}

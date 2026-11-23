<?php

namespace Modules\Website\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Mitra\Models\Mitra;
use Modules\Website\Models\Website;
use Modules\Website\Services\WebsiteService;

class FrontpageController extends Controller
{
    /**
     * Frontpage platform: http://domain/
     */
    public function platform()
    {
        return $this->render(Website::whereNull('mitra_id')->first(), null);
    }

    /**
     * Frontpage mitra: http://domain/{slug-mitra}
     */
    public function mitra(string $slug)
    {
        $mitra = Mitra::where('slug', $slug)->where('status', 'active')->firstOrFail();

        return $this->render(Website::where('mitra_id', $mitra->id)->first(), $mitra);
    }

    /**
     * Render frontpage. Draf hanya dapat dipratinjau oleh staf yang login.
     */
    protected function render(?Website $site, ?Mitra $mitra)
    {
        $canPreview = auth()->check();
        $useSite = $site && ($site->isPublished() || $canPreview);

        if (! $useSite && $mitra) {
            // Website mitra belum ada / belum dipublikasikan.
            abort(404);
        }

        $locale = app()->getLocale();

        if ($useSite) {
            $content = $site->contentFor($locale);
            $media = $site->media();
            $template = $site->template ?: 'default';
        } else {
            $all = WebsiteService::defaultContent()['locales'];
            $content = $all[$locale] ?? $all['id'];
            $media = [];
            $template = 'default';
        }

        return view('website::frontpage.site', [
            'site'      => $site,
            'mitra'     => $mitra,
            'content'   => $content,
            'media'     => $media,
            'template'  => $template,
            'brandName' => $mitra?->name ?? config('app.name'),
            'isPreview' => $useSite && ! $site->isPublished(),
        ]);
    }
}

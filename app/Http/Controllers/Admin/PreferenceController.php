<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mitra;
use Illuminate\Http\Request;

/**
 * Pengaturan preferensi user: bahasa, skin, dan mitra aktif.
 */
class PreferenceController extends Controller
{
    public function locale(Request $request)
    {
        $locale = $request->input('locale');
        abort_unless(array_key_exists($locale, config('lbacloud.locales')), 422);

        $request->session()->put('locale', $locale);
        if (auth()->check()) {
            auth()->user()->forceFill(['locale' => $locale])->save();
        }

        return back();
    }

    public function skin(Request $request)
    {
        $skin = $request->input('skin');
        $exists = \App\Models\Skin::where('code', $skin)->where('is_active', true)->exists();
        abort_unless($exists, 422);

        $request->session()->put('skin', $skin);
        if (auth()->check()) {
            auth()->user()->forceFill(['skin' => $skin])->save();
        }

        return back();
    }

    public function switchMitra(Request $request)
    {
        $mitra = Mitra::findOrFail($request->input('mitra_id'));
        $request->session()->put('active_mitra_id', $mitra->id);

        return back();
    }
}

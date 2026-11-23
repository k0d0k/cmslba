<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Mitra\Models\Mitra;
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
        $exists = \Modules\Setting\Models\Skin::where('code', $skin)->where('is_active', true)->exists();
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
        // Cabang terikat pada database mitra, jadi reset saat mitra berganti.
        $request->session()->forget('active_cabang_id');

        return back();
    }

    public function switchCabang(Request $request)
    {
        $cabangId = $request->input('cabang_id');
        if ($cabangId) {
            $request->session()->put('active_cabang_id', (int) $cabangId);
        } else {
            $request->session()->forget('active_cabang_id');
        }

        return back();
    }
}

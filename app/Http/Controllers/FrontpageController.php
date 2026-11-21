<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Services\TenantService;
use Illuminate\Support\Facades\DB;

class FrontpageController extends Controller
{
    /**
     * Frontpage publik per mitra: http://domain/{nama-mitra}
     */
    public function show(string $slug, TenantService $tenant)
    {
        $mitra = Mitra::where('slug', $slug)->where('status', 'active')->first();

        if (! $mitra) {
            abort(404);
        }

        // Sambungkan ke database mitra dan ambil data contoh sebagai bukti DB terpisah.
        $tenant->connect($mitra);

        $cabangCount = 0;
        $pelangganCount = 0;
        try {
            $cabangCount = DB::connection('tenant')->table('cabangs')->count();
            $pelangganCount = DB::connection('tenant')->table('pelanggans')->count();
        } catch (\Throwable $e) {
            //
        }

        return view('frontpage.mitra', [
            'mitra'          => $mitra,
            'cabangCount'    => $cabangCount,
            'pelangganCount' => $pelangganCount,
        ]);
    }
}

<?php

namespace App\Http\Middleware;

use Modules\Mitra\Models\Mitra;
use App\Services\TenantService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mengaktifkan koneksi database mitra (tenant) sesuai user yang login.
 * - User mitra  -> database mitranya sendiri.
 * - User pusat  -> mitra yang sedang dipilih (session) atau mitra aktif pertama (demo).
 */
class ResolveTenant
{
    public function __construct(protected TenantService $tenant)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user) {
            $mitra = null;

            if ($user->mitra_id) {
                $mitra = Mitra::find($user->mitra_id);
            } elseif ($user->isPrincipal()) {
                $mitraId = $request->session()->get('active_mitra_id');
                $mitra = $mitraId ? Mitra::find($mitraId) : Mitra::where('status', 'active')->orderBy('id')->first();
            }

            if ($mitra) {
                $this->tenant->connect($mitra);
                $request->session()->put('active_mitra_id', $mitra->id);
                app()->instance('active_mitra', $mitra);
            }
        }

        return $next($request);
    }
}

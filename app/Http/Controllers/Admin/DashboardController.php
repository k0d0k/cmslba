<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Mitra;
use App\Models\User;
use App\Models\Tenant\Pelanggan;
use App\Services\TenantService;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index(TenantService $tenant)
    {
        $user = auth()->user();
        $isPrincipal = $user?->isPrincipal() ?? false;
        $activeMitra = app()->bound('active_mitra') ? app('active_mitra') : null;

        $tenantStats = ['cabang' => 0, 'pelanggan' => 0];

        if ($activeMitra) {
            try {
                $tenantStats['cabang'] = DB::connection('tenant')->table('cabangs')->count();
                $tenantStats['pelanggan'] = DB::connection('tenant')->table('pelanggans')->count();
            } catch (\Throwable $e) {
                // database mitra belum siap
            }
        }

        if ($isPrincipal) {
            // Pusat: seluruh data platform.
            $cards = [
                ['label' => 'Users',  'value' => User::count(),  'color' => '#465fff'],
                ['label' => 'Roles',  'value' => Role::count(),  'color' => '#10b981'],
                ['label' => 'Menus',  'value' => Menu::count(),  'color' => '#f59e0b'],
                ['label' => 'Mitras', 'value' => Mitra::count(), 'color' => '#f43f5e'],
            ];
        } else {
            // Mitra: hanya data mitra sendiri.
            $mitraId = $activeMitra?->id ?? $user?->mitra_id;
            $cards = [
                ['label' => 'Users',     'value' => $mitraId ? User::where('mitra_id', $mitraId)->count() : 0, 'color' => '#465fff'],
                ['label' => 'Cabang',    'value' => $tenantStats['cabang'],    'color' => '#10b981'],
                ['label' => 'Pelanggan', 'value' => $tenantStats['pelanggan'], 'color' => '#f59e0b'],
                ['label' => 'Brand',     'value' => $activeMitra ? $activeMitra->brands()->count() : 0, 'color' => '#f43f5e'],
            ];
        }

        return view('admin.dashboard', compact('cards', 'tenantStats', 'activeMitra', 'isPrincipal'));
    }
}

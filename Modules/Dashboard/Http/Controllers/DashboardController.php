<?php

namespace Modules\Dashboard\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\TenantService;
use Modules\Menu\Models\Menu;
use Modules\Mitra\Models\Mitra;
use Modules\Pelanggan\Models\Pelanggan;
use Modules\User\Models\User;
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
                ['label' => __('dashboard.card_users'),  'value' => User::count(),  'color' => '#465fff'],
                ['label' => __('dashboard.card_roles'),  'value' => Role::count(),  'color' => '#10b981'],
                ['label' => __('dashboard.card_menus'),  'value' => Menu::count(),  'color' => '#f59e0b'],
                ['label' => __('dashboard.card_mitras'), 'value' => Mitra::count(), 'color' => '#f43f5e'],
            ];
        } else {
            // Mitra: hanya data mitra sendiri.
            $mitraId = $activeMitra?->id ?? $user?->mitra_id;
            $cards = [
                ['label' => __('dashboard.card_users'),     'value' => $mitraId ? User::where('mitra_id', $mitraId)->count() : 0, 'color' => '#465fff'],
                ['label' => __('dashboard.card_branch'),    'value' => $tenantStats['cabang'],    'color' => '#10b981'],
                ['label' => __('dashboard.card_customer'),  'value' => $tenantStats['pelanggan'], 'color' => '#f59e0b'],
                ['label' => __('dashboard.card_brand'),     'value' => $activeMitra ? $activeMitra->brands()->count() : 0, 'color' => '#f43f5e'],
            ];
        }

        return view('dashboard::dashboard', compact('cards', 'tenantStats', 'activeMitra', 'isPrincipal'));
    }
}

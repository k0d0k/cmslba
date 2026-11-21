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
        $stats = [
            'users'  => User::count(),
            'roles'  => Role::count(),
            'menus'  => Menu::count(),
            'mitras' => Mitra::count(),
        ];

        $tenantStats = ['cabang' => 0, 'pelanggan' => 0];
        $activeMitra = app()->bound('active_mitra') ? app('active_mitra') : null;

        if ($activeMitra) {
            try {
                $tenantStats['cabang'] = DB::connection('tenant')->table('cabangs')->count();
                $tenantStats['pelanggan'] = DB::connection('tenant')->table('pelanggans')->count();
            } catch (\Throwable $e) {
                // database mitra belum siap
            }
        }

        return view('admin.dashboard', compact('stats', 'tenantStats', 'activeMitra'));
    }
}

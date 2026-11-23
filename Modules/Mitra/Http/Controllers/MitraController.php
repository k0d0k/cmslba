<?php

namespace Modules\Mitra\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\TenantService;
use Modules\Brand\Models\Brand;
use Modules\Menu\Models\Menu;
use Modules\Menu\Services\MenuService;
use Modules\Mitra\Models\Mitra;
use Modules\Mitra\Models\MitraSubscription;
use Modules\Subscription\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class MitraController extends Controller
{
    public function __construct(protected TenantService $tenant)
    {
    }

    /** Cek hak akses aksi berdasarkan menu `mitras` di database pusat. */
    protected function can(string $ability): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }
        if ($user->hasRole('admin_principal')) {
            return true;
        }
        $menu = Menu::where('key', 'mitras')->first();

        return $menu ? app(MenuService::class)->userCan($user, $menu, $ability) : false;
    }

    protected function authorizeAbility(string $ability): void
    {
        abort_unless($this->can($ability), 403, __('messages.forbidden'));
    }

    public function index()
    {
        $this->authorizeAbility('view');

        return view('mitra::mitras.index', [
            'canCreate' => $this->can('create'),
            'canUpdate' => $this->can('update'),
            'canDelete' => $this->can('delete'),
        ]);
    }

    public function data()
    {
        $this->authorizeAbility('view');

        $query = Mitra::query()->with(['brands', 'subscriptions.subscription']);

        // Selain principal: hanya boleh melihat mitranya sendiri.
        $user = auth()->user();
        if ($user && ! $user->isPrincipal()) {
            $query->whereKey($user->mitra_id);
        }

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->editColumn('logo', function ($m) {
                if (! $m->logo) {
                    return '<span class="text-slate-400">-</span>';
                }
                $url = e(\Illuminate\Support\Facades\Storage::url($m->logo));

                return '<button type="button" class="js-image-preview inline-block align-middle" data-full-src="' . $url . '">'
                    . '<img src="' . $url . '" alt="" class="w-10 h-10 rounded object-cover border border-slate-200 cursor-pointer hover:opacity-80">'
                    . '</button>';
            })
            ->editColumn('status', fn ($m) => '<span class="badge badge-' . ($m->status === 'active' ? 'success' : 'warning') . '">' . $m->status . '</span>')
            ->addColumn('brand', fn ($m) => $m->brands->pluck('name')->implode(', '))
            ->addColumn('langganan', fn ($m) => $m->subscriptions->first()?->subscription?->name)
            ->addColumn('actions', function ($m) {
                $html = '<div class="flex flex-wrap gap-2 items-center">';
                $html .= '<a href="' . route('admin.sina.index', ['mitra' => $m->slug]) . '" class="btn btn-sm btn-secondary" title="' . e(__('sina.title')) . '">'
                    . '<i data-lucide="brain-circuit"></i> SINA AI</a>';
                if ($this->can('update')) {
                    $html .= '<a href="' . route('admin.mitras.edit', $m->slug) . '" class="btn btn-sm btn-primary">' . __('common.edit') . '</a>';
                }
                if ($this->can('delete')) {
                    $html .= '<button type="button" class="btn btn-sm btn-danger js-delete" data-url="' . route('admin.mitras.destroy', $m->slug) . '">' . __('common.delete') . '</button>';
                }
                $html .= '</div>';

                return $html;
            })
            ->rawColumns(['logo', 'status', 'actions'])
            ->toJson();
    }

    public function create()
    {
        $this->authorizeAbility('create');

        return view('mitra::mitras.form', [
            'mitra'         => null,
            'brands'        => Brand::orderBy('name')->get(),
            'subscriptions' => Subscription::where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeAbility('create');
        $data = $this->validated($request);
        $brandIds = $data['brand_ids'] ?? [];
        $primaryBrandId = $data['primary_brand_id'] ?? null;
        $initialSubscriptionId = $data['initial_subscription_id'] ?? null;
        $initialSeats = $data['initial_seats'] ?? 1;
        unset($data['brand_ids'], $data['primary_brand_id'], $data['initial_subscription_id'], $data['initial_seats']);

        // Logo ditangani terpisah (simpan berkas, bukan objek UploadedFile).
        unset($data['logo']);
        if ($logo = $this->storeLogo($request)) {
            $data['logo'] = $logo;
        }

        $initialPackage = $initialSubscriptionId
            ? Subscription::findOrFail($initialSubscriptionId)
            : null;
        if ($initialPackage?->seat_limit !== null && $initialSeats > $initialPackage->seat_limit) {
            throw ValidationException::withMessages([
                'initial_seats' => __('mitra.seat_exceed'),
            ]);
        }

        $mitra = Mitra::create($data);
        $this->syncBrands($mitra, $brandIds, $primaryBrandId);

        $this->tenant->provision($mitra);
        $this->seedTenant($mitra);

        if ($initialPackage) {
            $this->createSubscription($mitra, $initialPackage, (int) $initialSeats);
        }

        return redirect()->route('admin.mitras.index')
            ->with('success', __('messages.mitra_created', ['name' => $mitra->name, 'db' => $mitra->db_name]));
    }

    public function edit(Mitra $mitra)
    {
        $this->authorizeAbility('update');

        $mitra->load(['brands', 'subscriptions.subscription']);

        return view('mitra::mitras.form', [
            'mitra'         => $mitra,
            'brands'        => Brand::orderBy('name')->get(),
            'subscriptions' => Subscription::where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Mitra $mitra)
    {
        $this->authorizeAbility('update');
        $data = $this->validated($request, $mitra->id);
        unset($data['brand_ids'], $data['primary_brand_id'], $data['initial_subscription_id'], $data['initial_seats']);

        // Logo hanya diganti bila ada berkas baru diunggah.
        unset($data['logo']);
        if ($logo = $this->storeLogo($request)) {
            $data['logo'] = $logo;
        }

        $mitra->update($data);

        return redirect()->route('admin.mitras.index')->with('success', __('messages.updated'));
    }

    public function destroy(Mitra $mitra)
    {
        $this->authorizeAbility('delete');
        $this->tenant->drop($mitra);
        $mitra->delete();

        if (request()->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', __('messages.deleted'));
    }

    public function subscribe(Request $request, Mitra $mitra)
    {
        $this->authorizeAbility('update');
        $data = $request->validate([
            'subscriptions_id' => ['required', 'exists:subscription,id'],
            'seats' => ['required', 'integer', 'min:1'],
        ]);
        $package = Subscription::findOrFail($data['subscriptions_id']);

        if ($package->seat_limit !== null && $data['seats'] > $package->seat_limit) {
            return back()->withErrors([
                'seats' => __('mitra.seat_exceed'),
            ])->withInput();
        }

        $this->createSubscription($mitra, $package, (int) $data['seats']);

        return back()->with('success', __('mitra.subscription_added'));
    }

    public function cancelSubscription(Mitra $mitra, MitraSubscription $subscription)
    {
        $this->authorizeAbility('update');
        abort_unless((int) $subscription->mitra_id === (int) $mitra->id, 404);

        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return back()->with('success', __('mitra.subscription_cancelled'));
    }

    public function activateSubscription(Mitra $mitra, MitraSubscription $subscription)
    {
        $this->authorizeAbility('update');
        abort_unless((int) $subscription->mitra_id === (int) $mitra->id, 404);

        $start = now();
        $subscription->update([
            'status' => 'active',
            'starts_at' => $subscription->starts_at ?? $start,
            'current_period_start' => $start,
            'current_period_end' => $this->periodEnd($subscription->subscription, $start),
            'cancelled_at' => null,
            'ended_at' => null,
        ]);

        return back()->with('success', __('mitra.subscription_activated'));
    }

    public function attachBrand(Request $request, Mitra $mitra)
    {
        $this->authorizeAbility('update');
        $data = $request->validate([
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'is_primary' => ['nullable', 'boolean'],
        ]);
        $brandId = (int) $data['brand_id'];
        $existingPivot = DB::table('mitra_brands')
            ->where('mitra_id', $mitra->id)
            ->where('brand_id', $brandId)
            ->first();
        $isPrimary = $request->boolean('is_primary')
            || (! $existingPivot && $mitra->brands()->count() === 0)
            || ($existingPivot && (bool) $existingPivot->is_primary);

        if ($isPrimary) {
            DB::table('mitra_brands')->where('mitra_id', $mitra->id)->update([
                'is_primary' => false,
                'updated_at' => now(),
            ]);
        }

        $mitra->brands()->syncWithoutDetaching([
            $brandId => ['is_primary' => $isPrimary],
        ]);

        return back()->with('success', __('mitra.brand_attached'));
    }

    public function makePrimaryBrand(Mitra $mitra, Brand $brand)
    {
        $this->authorizeAbility('update');
        abort_unless($mitra->brands()->whereKey($brand->id)->exists(), 404);

        DB::table('mitra_brands')->where('mitra_id', $mitra->id)->update([
            'is_primary' => false,
            'updated_at' => now(),
        ]);
        $mitra->brands()->updateExistingPivot($brand->id, [
            'is_primary' => true,
            'updated_at' => now(),
        ]);

        return back()->with('success', __('mitra.brand_primary_updated'));
    }

    public function detachBrand(Mitra $mitra, Brand $brand)
    {
        $this->authorizeAbility('update');
        $pivot = DB::table('mitra_brands')
            ->where('mitra_id', $mitra->id)
            ->where('brand_id', $brand->id)
            ->first();
        abort_unless($pivot, 404);

        $mitra->brands()->detach($brand->id);

        if ($pivot->is_primary) {
            $nextBrand = $mitra->brands()->orderBy('brands.name')->first();
            if ($nextBrand) {
                $mitra->brands()->updateExistingPivot($nextBrand->id, [
                    'is_primary' => true,
                    'updated_at' => now(),
                ]);
            }
        }

        return back()->with('success', __('mitra.brand_detached'));
    }

    protected function syncBrands(Mitra $mitra, array $brandIds, mixed $primaryBrandId = null): void
    {
        $brandIds = array_values(array_unique(array_map('intval', $brandIds)));
        if (! $brandIds) {
            return;
        }

        $primaryBrandId = in_array((int) $primaryBrandId, $brandIds, true)
            ? (int) $primaryBrandId
            : $brandIds[0];
        $sync = [];
        foreach ($brandIds as $brandId) {
            $sync[$brandId] = ['is_primary' => $brandId === $primaryBrandId];
        }
        $mitra->brands()->sync($sync);
    }

    protected function createSubscription(Mitra $mitra, Subscription $package, int $seats): MitraSubscription
    {
        if ($package->seat_limit !== null && $seats > $package->seat_limit) {
            throw ValidationException::withMessages([
                'seats' => __('mitra.seat_exceed'),
            ]);
        }

        $start = now();
        $trialEnd = $package->trial_days > 0 ? $start->copy()->addDays($package->trial_days) : null;

        $subscription = $mitra->subscriptions()->create([
            'subscriptions_id' => $package->id,
            'status' => $trialEnd ? 'trialing' : 'active',
            'seats' => $seats,
            'starts_at' => $start,
            'trial_ends_at' => $trialEnd,
            'current_period_start' => $start,
            'current_period_end' => $this->periodEnd($package, $start),
        ]);

        // Beri role default mitra kemampuan penuh atas menu yang termasuk
        // modul pada paket ini, agar menunya otomatis tampil setelah login.
        app(MenuService::class)->grantPackageMenusToDefaultMitraRole($package);

        return $subscription;
    }

    protected function periodEnd(Subscription $package, $start)
    {
        return match ($package->billing_interval) {
            'yearly' => $start->copy()->addYearNoOverflow(),
            'weekly' => $start->copy()->addWeek(),
            'daily' => $start->copy()->addDay(),
            default => $start->copy()->addMonthNoOverflow(),
        };
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name'   => ['required', 'string', 'max:150'],
            'slug'   => ['nullable', 'string', 'max:150', 'unique:mitras,slug' . ($ignoreId ? ",{$ignoreId}" : '')],
            'email'  => ['nullable', 'email'],
            'phone'  => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'logo'   => ['nullable', 'image', 'max:2048'],
            'skin'   => ['nullable', 'string'],
            'locale' => ['nullable', 'in:id,en,ms'],
            'status' => ['required', 'in:pending,active,suspended'],
            'plan'   => ['nullable', 'string'],
            'brand_ids' => ['nullable', 'array'],
            'brand_ids.*' => ['integer', 'distinct', 'exists:brands,id'],
            'primary_brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'initial_subscription_id' => ['nullable', 'exists:subscription,id'],
            'initial_seats' => ['required_with:initial_subscription_id', 'integer', 'min:1'],
        ]);
    }

    /**
     * Simpan berkas logo bila ada di request, lalu kembalikan path-nya.
     * Bila tidak ada berkas baru, kembalikan null (agar logo lama tetap dipakai).
     */
    protected function storeLogo(Request $request): ?string
    {
        if ($request->hasFile('logo')) {
            return $request->file('logo')->store('uploads/mitras', 'public');
        }

        return null;
    }

    protected function seedTenant(Mitra $mitra): void
    {
        $this->tenant->connect($mitra);

        DB::connection('tenant')->table('cabangs')->insert([
            'name'       => 'Cabang Pusat',
            'slug'       => 'cabang-pusat',
            'code'       => 'PST',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

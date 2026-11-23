<?php

namespace Modules\Sina\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Menu\Models\Menu;
use Modules\Menu\Services\MenuService;
use Modules\Mitra\Models\Mitra;
use Modules\Setting\Models\Setting;
use Modules\Sina\Models\AiProviderConfig;
use Modules\Sina\Models\AiTopup;
use Modules\Sina\Models\AiUsageRecord;
use Modules\Sina\Services\SinaCreditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

/**
 * Integrasi SINA AI (Data Master).
 *
 * Halaman bertab:
 *  - EndPoin Penyedia SINA : konfigurasi penyedia AI per mitra.
 *  - Token & Harga         : harga token per kredit (global, dipakai sebagai
 *                            dasar perhitungan top-up).
 *  - Saldo Kredit SINA AI  : saldo kredit + riwayat pemakaian per mitra.
 *  - Tambah Kredit         : top-up kredit per mitra beserta riwayatnya.
 */
class SinaController extends Controller
{
    public function __construct(protected SinaCreditService $credits)
    {
    }

    /** Cek hak akses aksi berdasarkan menu `sina_ai` di database pusat. */
    protected function can(string $ability): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }
        if ($user->hasRole('admin_principal')) {
            return true;
        }
        $menu = Menu::where('key', 'sina_ai')->first();

        return $menu ? app(MenuService::class)->userCan($user, $menu, $ability) : false;
    }

    protected function authorizeAbility(string $ability): void
    {
        abort_unless($this->can($ability), 403, __('messages.forbidden'));
    }

    /**
     * Mitra yang sedang dikelola pada halaman ini.
     * Principal bebas memilih mitra lewat query `mitra` (berisi slug) atau
     * input `mitra_id`, sedangkan user mitra selalu terkunci pada mitranya.
     */
    protected function resolveMitra(Request $request): ?Mitra
    {
        $user = auth()->user();

        if ($user && ! $user->isPrincipal()) {
            return Mitra::find($user->mitra_id)
                ?? (app()->bound('active_mitra') ? app('active_mitra') : null);
        }

        $slug = $request->query('mitra');
        if ($slug && $mitra = Mitra::where('slug', $slug)->first()) {
            return $mitra;
        }

        $id = $request->input('mitra_id');
        if ($id && $mitra = Mitra::find($id)) {
            return $mitra;
        }

        return app()->bound('active_mitra')
            ? app('active_mitra')
            : Mitra::orderBy('id')->first();
    }

    /** Setelan harga/tagihan kredit SINA (global). */
    protected function pricing(): array
    {
        return [
            'billing_enabled'  => (bool) Setting::get('sina_credit_billing_enabled', true, 'sina'),
            'credit_per_chat'  => (int) Setting::get('sina_credit_per_chat', 1, 'sina'),
            'credit_per_1000'  => (int) Setting::get('sina_credit_per_1000_tokens', 1, 'sina'),
            'unit'             => (string) Setting::get('sina_unit', 'sina_credit', 'sina'),
        ];
    }

    public function index(Request $request)
    {
        $this->authorizeAbility('view');

        $user = auth()->user();
        $isPrincipal = $user->isPrincipal();
        $mitra = $this->resolveMitra($request);
        $wallet = $mitra ? $this->credits->walletFor($mitra) : null;
        $config = $mitra
            ? AiProviderConfig::where('mitra_id', $mitra->id)->orderByDesc('is_default')->first()
            : null;

        return view('sina::sina.index', [
            'mitra'          => $mitra,
            'mitras'         => $isPrincipal ? Mitra::orderBy('name')->get() : collect(),
            'isPrincipal'    => $isPrincipal,
            'wallet'         => $wallet,
            'config'         => $config,
            'pricing'        => $this->pricing(),
            'canUpdate'      => $this->can('update'),
            'canManagePrice' => $isPrincipal && $this->can('update'),
            'providers'      => $this->providerOptions(),
        ]);
    }

    /** Riwayat pemakaian kredit (DataTables). */
    public function data(Request $request)
    {
        $this->authorizeAbility('view');

        $mitra = $this->resolveMitra($request);
        $query = AiUsageRecord::query()
            ->when($mitra, fn ($q) => $q->where('mitra_id', $mitra->id), fn ($q) => $q->whereRaw('1 = 0'));

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->editColumn('recorded_at', fn ($r) => optional($r->recorded_at)->format('Y-m-d H:i'))
            ->addColumn('total_credits', fn ($r) => number_format((int) $r->unit_price_credits * (int) $r->credit_units))
            ->rawColumns([])
            ->toJson();
    }

    /** Riwayat top-up kredit (DataTables). */
    public function topups(Request $request)
    {
        $this->authorizeAbility('view');

        $mitra = $this->resolveMitra($request);
        $query = AiTopup::query()
            ->when($mitra, fn ($q) => $q->where('mitra_id', $mitra->id), fn ($q) => $q->whereRaw('1 = 0'));

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->editColumn('created_at', fn ($r) => optional($r->created_at)->format('Y-m-d H:i'))
            ->editColumn('credits', fn ($r) => number_format((int) $r->credits))
            ->editColumn('price_per_credit', fn ($r) => number_format((int) $r->price_per_credit) . ' /chat')
            ->editColumn('total_amount', fn ($r) => number_format((int) $r->total_amount))
            ->rawColumns([])
            ->toJson();
    }

    /** Simpan konfigurasi penyedia AI untuk mitra terpilih. */
    public function updateConfig(Request $request)
    {
        $this->authorizeAbility('update');
        $mitra = $this->resolveMitra($request);
        abort_unless($mitra, 404);

        $data = $request->validate([
            'provider'   => ['required', 'string', 'max:255'],
            'label'      => ['nullable', 'string', 'max:255'],
            'base_url'   => ['required', 'string', 'max:255'],
            'model'      => ['required', 'string', 'max:255'],
            'api_key'    => ['nullable', 'string'],
            'enabled'    => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $config = AiProviderConfig::where('mitra_id', $mitra->id)->orderByDesc('is_default')->first()
            ?? new AiProviderConfig(['mitra_id' => $mitra->id]);

        if ($request->boolean('is_default')) {
            AiProviderConfig::where('mitra_id', $mitra->id)->update(['is_default' => false]);
        }

        $config->fill([
            'provider'   => $data['provider'],
            'label'      => $data['label'] ?? null,
            'base_url'   => $data['base_url'],
            'model'      => $data['model'],
            'api_key'    => $data['api_key'] ?? null,
            'enabled'    => $request->boolean('enabled', true),
            'is_default' => $request->boolean('is_default'),
        ])->save();

        return back()->with('success', __('sina.flash_config_saved'));
    }

    /** Simpan setelan harga/tagihan kredit SINA (global, semua mitra). */
    public function savePricing(Request $request)
    {
        $this->authorizeAbility('update');
        abort_unless(auth()->user()->isPrincipal(), 403, __('messages.forbidden'));

        $data = $request->validate([
            'billing_enabled'        => ['nullable', 'boolean'],
            'sina_credit_per_chat'   => ['required', 'integer', 'min:1'],
            'sina_credit_per_1000_tokens' => ['required', 'integer', 'min:1'],
            'sina_unit'              => ['required', 'string', 'max:50'],
        ]);

        Setting::put('sina_credit_billing_enabled', $request->boolean('billing_enabled') ? 1 : 0, 'sina');
        Setting::put('sina_credit_per_chat', $data['sina_credit_per_chat'], 'sina');
        Setting::put('sina_credit_per_1000_tokens', $data['sina_credit_per_1000_tokens'], 'sina');
        Setting::put('sina_unit', $data['sina_unit'], 'sina');

        return back()->with('success', __('sina.flash_pricing_saved'));
    }

    /** Tambah kredit (top-up) mitra terpilih dan catat riwayatnya. */
    public function topUp(Request $request)
    {
        $this->authorizeAbility('update');
        $mitra = $this->resolveMitra($request);
        abort_unless($mitra, 404);

        $data = $request->validate([
            'credits' => ['required', 'integer', 'min:1'],
            'note'    => ['nullable', 'string', 'max:255'],
        ]);

        $credits = (int) $data['credits'];
        $pricing = $this->pricing();
        $wallet = $this->credits->walletFor($mitra);

        DB::connection('central')->transaction(function () use ($wallet, $mitra, $credits, $pricing, $data) {
            $this->credits->topUp($wallet, $credits);

            AiTopup::create([
                'mitra_id'         => $mitra->id,
                'wallet_id'        => $wallet->id,
                'credits'          => $credits,
                // Snapshot aturan tagihan saat top-up (1 chat = credit_per_chat token).
                'price_per_credit' => $pricing['credit_per_chat'],
                'total_amount'     => $pricing['credit_per_chat'] * $credits,
                'note'             => $data['note'] ?? null,
                'created_by'       => auth()->id(),
            ]);
        });

        return back()->with('success', __('sina.flash_topup_added'));
    }

    /** Pilihan penyedia AI yang didukung. */
    protected function providerOptions(): array
    {
        return [
            'ollama'    => __('sina.provider_ollama'),
            'openai'    => __('sina.provider_openai'),
            'azure'     => __('sina.provider_azure'),
            'anthropic' => __('sina.provider_anthropic'),
            'gemini'    => __('sina.provider_gemini'),
            'groq'      => __('sina.provider_groq'),
            'custom'    => __('sina.provider_custom'),
        ];
    }
}

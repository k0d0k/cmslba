<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiProviderConfig;
use App\Models\AiTopup;
use App\Models\AiUsageRecord;
use App\Models\Menu;
use App\Models\Mitra;
use App\Models\Setting;
use App\Services\MenuService;
use App\Services\SinaCreditService;
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
    /** Kunci setelan harga token global (berlaku untuk semua mitra). */
    public const TOKEN_PRICE_KEY = 'token_price_per_credit';

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
     * Principal bebas memilih mitra (dari query `mitra`/input `mitra_id`),
     * sedangkan user mitra selalu terkunci pada mitra miliknya.
     */
    protected function resolveMitra(Request $request): ?Mitra
    {
        $user = auth()->user();

        if ($user && ! $user->isPrincipal()) {
            return Mitra::find($user->mitra_id)
                ?? (app()->bound('active_mitra') ? app('active_mitra') : null);
        }

        $id = $request->input('mitra_id') ?? $request->query('mitra');
        if ($id && $mitra = Mitra::find($id)) {
            return $mitra;
        }

        return app()->bound('active_mitra')
            ? app('active_mitra')
            : Mitra::orderBy('id')->first();
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

        return view('admin.sina.index', [
            'mitra'          => $mitra,
            'mitras'         => $isPrincipal ? Mitra::orderBy('name')->get() : collect(),
            'isPrincipal'    => $isPrincipal,
            'wallet'         => $wallet,
            'config'         => $config,
            'tokenPrice'     => (float) Setting::get(self::TOKEN_PRICE_KEY, 0, 'sina'),
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
            ->editColumn('price_per_credit', fn ($r) => 'Rp ' . number_format((float) $r->price_per_credit, 0, ',', '.'))
            ->editColumn('total_amount', fn ($r) => 'Rp ' . number_format((float) $r->total_amount, 0, ',', '.'))
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

        return back()->with('success', 'Konfigurasi EndPoin Penyedia SINA berhasil disimpan.');
    }

    /** Simpan harga token per kredit (global, semua mitra). */
    public function saveTokenPrice(Request $request)
    {
        $this->authorizeAbility('update');
        abort_unless(auth()->user()->isPrincipal(), 403, __('messages.forbidden'));

        $data = $request->validate([
            self::TOKEN_PRICE_KEY => ['required', 'numeric', 'min:0'],
        ]);

        Setting::put(self::TOKEN_PRICE_KEY, $data[self::TOKEN_PRICE_KEY], 'sina');

        return back()->with('success', 'Harga token SINA AI berhasil disimpan.');
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
        $price = (float) Setting::get(self::TOKEN_PRICE_KEY, 0, 'sina');
        $wallet = $this->credits->walletFor($mitra);

        DB::connection('central')->transaction(function () use ($wallet, $mitra, $credits, $price, $data) {
            $this->credits->topUp($wallet, $credits);

            AiTopup::create([
                'mitra_id'         => $mitra->id,
                'wallet_id'        => $wallet->id,
                'credits'          => $credits,
                'price_per_credit' => $price,
                'total_amount'     => $price * $credits,
                'note'             => $data['note'] ?? null,
                'created_by'       => auth()->id(),
            ]);
        });

        return back()->with('success', 'Kredit SINA AI berhasil ditambahkan.');
    }

    /** Pilihan penyedia AI yang didukung. */
    protected function providerOptions(): array
    {
        return [
            'ollama'    => 'Ollama (lokal)',
            'openai'    => 'OpenAI',
            'azure'     => 'Azure OpenAI',
            'anthropic' => 'Anthropic Claude',
            'gemini'    => 'Google Gemini',
            'groq'      => 'Groq',
            'custom'    => 'Custom (OpenAI compatible)',
        ];
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiProviderConfig;
use App\Models\AiUsageRecord;
use App\Models\Menu;
use App\Models\Mitra;
use App\Services\MenuService;
use App\Services\SinaCreditService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

/**
 * Integrasi SINA AI (Data Master): konfigurasi penyedia, saldo kredit,
 * riwayat pemakaian, dan top-up kredit per mitra aktif.
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

    /** Mitra yang sedang aktif pada sesi (di-bind oleh middleware ResolveTenant). */
    protected function activeMitra(): ?Mitra
    {
        return app()->bound('active_mitra') ? app('active_mitra') : null;
    }

    public function index()
    {
        $this->authorizeAbility('view');

        $mitra = $this->activeMitra();
        $wallet = $mitra ? $this->credits->walletFor($mitra) : null;
        $config = $mitra
            ? AiProviderConfig::where('mitra_id', $mitra->id)->orderByDesc('is_default')->first()
            : null;

        return view('admin.sina.index', [
            'mitra'      => $mitra,
            'wallet'     => $wallet,
            'config'     => $config,
            'canUpdate'  => $this->can('update'),
            'providers'  => $this->providerOptions(),
        ]);
    }

    public function data()
    {
        $this->authorizeAbility('view');

        $mitra = $this->activeMitra();
        $query = AiUsageRecord::query()
            ->when($mitra, fn ($q) => $q->where('mitra_id', $mitra->id), fn ($q) => $q->whereRaw('1 = 0'));

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->editColumn('recorded_at', fn ($r) => optional($r->recorded_at)->format('Y-m-d H:i'))
            ->addColumn('total_credits', fn ($r) => number_format((int) $r->unit_price_credits * (int) $r->credit_units))
            ->rawColumns([])
            ->toJson();
    }

    public function updateConfig(Request $request)
    {
        $this->authorizeAbility('update');
        $mitra = $this->activeMitra();
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

        return back()->with('success', 'Konfigurasi SINA AI berhasil disimpan.');
    }

    public function topUp(Request $request)
    {
        $this->authorizeAbility('update');
        $mitra = $this->activeMitra();
        abort_unless($mitra, 404);

        $data = $request->validate([
            'credits' => ['required', 'integer', 'min:1'],
        ]);

        $wallet = $this->credits->walletFor($mitra);
        $this->credits->topUp($wallet, (int) $data['credits']);

        return back()->with('success', 'Saldo kredit SINA AI berhasil ditambah.');
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

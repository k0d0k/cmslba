<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Menu;
use App\Services\MenuService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

/**
 * Jejak Audit (Audit Trail) — Monitoring (Pengaturan).
 *
 * Menampilkan seluruh aktivitas aplikasi yang tercatat: login/logout,
 * navigasi halaman, aksi ubah data (tambah/ubah/hapus), hingga error/bug.
 */
class AuditTrailController extends Controller
{
    /** Cek hak akses berdasarkan menu `audit_trail`. */
    protected function can(string $ability): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }
        if ($user->hasRole('admin_principal')) {
            return true;
        }
        $menu = Menu::where('key', 'audit_trail')->first();

        return $menu ? app(MenuService::class)->userCan($user, $menu, $ability) : false;
    }

    protected function authorizeAbility(string $ability): void
    {
        abort_unless($this->can($ability), 403, __('messages.forbidden'));
    }

    public function index(Request $request)
    {
        $this->authorizeAbility('view');

        return view('admin.audit.index', [
            'title'   => 'Jejak Audit',
            'filters' => [
                'event' => $request->query('event'),
                'from'  => $request->query('from'),
                'to'    => $request->query('to'),
            ],
            'events' => $this->eventOptions(),
        ]);
    }

    public function data(Request $request)
    {
        $this->authorizeAbility('view');

        $query = AuditLog::query()->orderByDesc('created_at');

        // User non-principal hanya melihat aktivitas mitranya sendiri.
        $user = auth()->user();
        if ($user && ! $user->isPrincipal()) {
            $query->where('mitra_id', $user->mitra_id);
        }

        if ($event = $request->query('event')) {
            $query->where('event', $event);
        }
        if ($from = $request->query('from')) {
            $query->where('created_at', '>=', $from . ' 00:00:00');
        }
        if ($to = $request->query('to')) {
            $query->where('created_at', '<=', $to . ' 23:59:59');
        }

        $dt = DataTables::eloquent($query)->addIndexColumn();

        $dt->editColumn('created_at', fn ($row) => optional($row->created_at)->format('d-m-Y H:i:s'));
        $dt->editColumn('event', fn ($row) => '<span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium '
            . $row->eventBadgeClass() . '">' . e($row->eventLabel()) . '</span>');
        $dt->editColumn('user_name', fn ($row) => $row->user_name
            ? e($row->user_name) . ($row->user_email ? '<div class="text-xs text-slate-400">' . e($row->user_email) . '</div>' : '')
            : '<span class="text-slate-400">-</span>');
        $dt->editColumn('description', fn ($row) => e(\Illuminate\Support\Str::limit((string) $row->description, 90)));

        $dt->addColumn('detail', fn ($row) => '<button type="button" class="btn btn-sm btn-secondary js-audit-detail" data-url="'
            . route('admin.audit.show', $row->id) . '">' . __('common.detail') . '</button>');

        $dt->rawColumns(['event', 'user_name', 'description', 'detail']);

        return $dt->toJson();
    }

    /** Detail satu baris untuk ditampilkan pada modal. */
    public function show($id)
    {
        $this->authorizeAbility('view');

        $log = AuditLog::findOrFail($id);

        $user = auth()->user();
        if ($user && ! $user->isPrincipal() && (int) $log->mitra_id !== (int) $user->mitra_id) {
            abort(403, __('messages.forbidden'));
        }

        return response()->json([
            'id'          => $log->id,
            'created_at'  => optional($log->created_at)->format('d-m-Y H:i:s'),
            'event'       => $log->event,
            'event_label' => $log->eventLabel(),
            'badge_class' => $log->eventBadgeClass(),
            'user_name'   => $log->user_name,
            'user_email'  => $log->user_email,
            'mitra_id'    => $log->mitra_id,
            'module'      => $log->module,
            'description' => $log->description,
            'subject'     => $log->subject_type ? $log->subject_type . '#' . $log->subject_id : null,
            'method'      => $log->method,
            'route_name'  => $log->route_name,
            'url'         => $log->url,
            'status_code' => $log->status_code,
            'ip'          => $log->ip,
            'user_agent'  => $log->user_agent,
            'properties'  => $log->properties,
        ]);
    }

    /** Opsi jenis kejadian untuk filter. */
    protected function eventOptions(): array
    {
        return [
            'login'        => 'Login',
            'logout'       => 'Logout',
            'login_failed' => 'Login Gagal',
            'create'       => 'Tambah Data',
            'update'       => 'Ubah Data',
            'delete'       => 'Hapus Data',
            'view'         => 'Lihat Halaman',
            'request'      => 'Aksi',
            'error'        => 'Error',
        ];
    }
}

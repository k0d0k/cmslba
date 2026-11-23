<?php

namespace Modules\Audit\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\User\Models\User;

/**
 * Catatan jejak audit. Tersimpan di koneksi `central`.
 */
class AuditLog extends Model
{
    protected $connection = 'central';

    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'mitra_id',
        'event',
        'module',
        'description',
        'subject_type',
        'subject_id',
        'method',
        'route_name',
        'url',
        'status_code',
        'ip',
        'user_agent',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /** Label jenis kejadian (untuk tampilan, mengikuti bahasa aktif). */
    public function eventLabel(): string
    {
        $key = 'audit.events.' . $this->event;
        $label = __($key);

        // Fallback bila kunci terjemahan tidak ditemukan.
        return $label === $key ? ucfirst((string) $this->event) : $label;
    }

    /** Kelas warna badge per jenis kejadian. */
    public function eventBadgeClass(): string
    {
        return match ($this->event) {
            'login'   => 'bg-emerald-100 text-emerald-700',
            'logout'  => 'bg-slate-100 text-slate-600',
            'login_failed', 'error' => 'bg-red-100 text-red-700',
            'create'  => 'bg-teal-100 text-teal-700',
            'update'  => 'bg-amber-100 text-amber-700',
            'delete'  => 'bg-rose-100 text-rose-700',
            default   => 'bg-slate-100 text-slate-600',
        };
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

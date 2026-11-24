<?php

namespace Modules\Practitioners\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Praktisi (tenaga medis/profesional) pada database mitra (tenant).
 * Skema mengikuti DDL: id ULID char(26); user_id mengacu ke akun user pusat.
 */
class Practitioner extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'practitioners';

    protected $fillable = [
        'user_id', 'display_name', 'title', 'professional_body', 'specialities',
        'status', 'verified_at', 'verified_by', 'expires_at', 'suspended_at',
        'archived_at', 'notes', 'created_by',
    ];

    protected $casts = [
        'specialities' => 'array',
        'verified_at'  => 'datetime',
        'expires_at'   => 'datetime',
        'suspended_at' => 'datetime',
        'archived_at'  => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Practitioner $practitioner) {
            if (blank($practitioner->created_by) && auth()->check()) {
                $practitioner->created_by = (string) auth()->id();
            }
        });
    }

    /** Akun user pusat pemilik praktisi ini. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\Modules\User\Models\User::class, 'user_id');
    }

    public function credentials(): HasMany
    {
        return $this->hasMany(PractitionerCredential::class, 'practitioner_id');
    }

    public function branchAuthorizations(): HasMany
    {
        return $this->hasMany(PractitionerBranchAuthorization::class, 'practitioner_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(PractitionerAuditLog::class, 'practitioner_id');
    }
}

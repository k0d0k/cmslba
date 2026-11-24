<?php

namespace Modules\Practitioners\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Kredensial/izin praktik (STR, SIP, dsb.) milik seorang praktisi.
 */
class PractitionerCredential extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'practitioner_credentials';

    protected $fillable = [
        'practitioner_id', 'credential_type', 'jurisdiction', 'reference_number',
        'issued_at', 'expires_at', 'status', 'verified_at', 'verified_by',
        'verification_notes', 'evidence_disk', 'evidence_path', 'revoked_at', 'created_by',
    ];

    protected $casts = [
        'issued_at'   => 'date',
        'expires_at'  => 'date',
        'verified_at' => 'datetime',
        'revoked_at'  => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (PractitionerCredential $credential) {
            if (blank($credential->created_by) && auth()->check()) {
                $credential->created_by = (string) auth()->id();
            }
        });
    }

    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class, 'practitioner_id');
    }
}

<?php

namespace Modules\Practitioners\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Otorisasi praktisi pada sebuah cabang mitra.
 */
class PractitionerBranchAuthorization extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'practitioner_branch_authorizations';

    protected $fillable = [
        'practitioner_id', 'branch_id', 'status', 'authorized_by',
        'authorized_at', 'revoked_at',
    ];

    protected $casts = [
        'authorized_at' => 'datetime',
        'revoked_at'    => 'datetime',
    ];

    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class, 'practitioner_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(\Modules\Cabang\Models\Cabang::class, 'branch_id');
    }
}

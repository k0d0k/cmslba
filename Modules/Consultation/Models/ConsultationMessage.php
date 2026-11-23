<?php

namespace Modules\Consultation\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Pesan dalam sebuah konsultasi (termasuk pesan asisten/AI dan pengguna).
 */
class ConsultationMessage extends Model
{
    use HasUlids;

    protected $connection = 'tenant';

    protected $table = 'consultation_messages';

    protected $fillable = [
        'consultation_id', 'role', 'content', 'provider', 'model',
        'prompt_tokens', 'completion_tokens', 'credits', 'created_by',
    ];

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class, 'consultation_id');
    }
}

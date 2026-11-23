<?php

namespace Modules\PaymentProvider\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Model;

/**
 * Konfigurasi payment gateway (database pusat).
 * Kredensial di-cast `encrypted` sehingga tersimpan terenkripsi di database.
 */
class PaymentProvider extends Model
{
    use HasSlug;

    protected $connection = 'central';

    protected $table = 'payment_providers';

    protected $fillable = [
        'slug',
        'provider',
        'label',
        'mode',
        'is_active',
        'currency',
        'server_key',
        'client_key',
        'api_key',
        'virtual_account',
        'client_id',
        'client_secret',
        'webhook_id',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'server_key'    => 'encrypted',
        'client_key'    => 'encrypted',
        'api_key'       => 'encrypted',
        'virtual_account' => 'encrypted',
        'client_id'     => 'encrypted',
        'client_secret' => 'encrypted',
        'webhook_id'    => 'encrypted',
    ];

    /** Kolom kredensial yang tidak boleh ditampilkan / dikosongkan saat update. */
    public const SECRET_COLUMNS = [
        'server_key',
        'client_key',
        'api_key',
        'virtual_account',
        'client_id',
        'client_secret',
        'webhook_id',
    ];

    public $slug_source = 'label';

    /** Label penyedia yang tersedia. */
    public static function providerOptions(): array
    {
        return [
            'midtrans' => 'Midtrans',
            'doku'     => 'Doku',
            'ipaymu'   => 'iPaymu',
            'paypal'   => 'PayPal',
        ];
    }
}

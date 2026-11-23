<?php

namespace Modules\Audit\Services;

use Modules\Audit\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

/**
 * Pencatat jejak audit aplikasi.
 *
 * Semua penulisan dibungkus try/catch dengan sengaja: kegagalan pencatatan
 * TIDAK boleh mengganggu jalannya aplikasi (mis. saat tabel belum dimigrasi).
 */
class AuditLogger
{
    /** Field yang tidak boleh ikut tercatat (rahasia). */
    protected const SENSITIVE_KEYS = [
        'password', 'password_confirmation', 'current_password', 'new_password',
        '_token', '_method', 'api_key', 'server_key', 'client_key', 'client_secret',
        'secret', 'token', 'access_token', 'private_key', 'webhook_secret',
    ];

    /** Panjang maksimum nilai pada properties (karakter). */
    protected const MAX_VALUE_LENGTH = 500;

    /**
     * Tulis satu baris jejak audit.
     *
     * @param  array{
     *     event:string,
     *     description?:?string,
     *     module?:?string,
     *     user?:?\Modules\User\Models\User,
     *     subject_type?:?string,
     *     subject_id?:?string|int,
     *     properties?:?array,
     *     method?:?string,
     *     route_name?:?string,
     *     url?:?string,
     *     status_code?:?int,
     *     ip?:?string,
     *     user_agent?:?string
     * }  $attrs
     */
    public function log(array $attrs): ?AuditLog
    {
        try {
            $request = request();

            $user = $attrs['user'] ?? Auth::user();

            $data = [
                'user_id'      => $user?->getKey() ?? ($attrs['user_id'] ?? null),
                'user_name'    => $user?->name ?? ($attrs['user_name'] ?? null),
                'user_email'   => $user?->email ?? ($attrs['user_email'] ?? null),
                'mitra_id'     => $attrs['mitra_id'] ?? $this->resolveMitraId($user),
                'event'        => (string) ($attrs['event'] ?? 'request'),
                'module'       => $attrs['module'] ?? $this->guessModule($request),
                'description'  => $attrs['description'] ?? null,
                'subject_type' => $attrs['subject_type'] ?? null,
                'subject_id'   => isset($attrs['subject_id']) ? (string) $attrs['subject_id'] : null,
                'method'       => $attrs['method'] ?? $request?->method(),
                'route_name'   => $attrs['route_name'] ?? $request?->route()?->getName(),
                'url'          => $attrs['url'] ?? $request?->fullUrl(),
                'status_code'  => $attrs['status_code'] ?? null,
                'ip'           => $attrs['ip'] ?? $request?->ip(),
                'user_agent'   => mb_substr((string) ($attrs['user_agent'] ?? $request?->userAgent()), 0, 500),
                'properties'   => $this->sanitize($attrs['properties'] ?? null),
                'created_at'   => now(),
                'updated_at'   => now(),
            ];

            return AuditLog::create($data);
        } catch (Throwable $e) {
            // Diamkan: pencatatan audit tidak boleh menghentikan aplikasi.
            return null;
        }
    }

    /**
     * Catat error/bug dari exception.
     */
    public function logError(Throwable $e, ?Request $request = null): ?AuditLog
    {
        $request ??= request();

        return $this->log([
            'event'       => 'error',
            'module'      => $this->guessModule($request),
            'description' => get_class($e) . ': ' . $e->getMessage(),
            'url'         => $request?->fullUrl(),
            'method'      => $request?->method(),
            'properties'  => [
                'exception' => get_class($e),
                'message'   => $e->getMessage(),
                'file'      => $e->getFile() . ':' . $e->getLine(),
                'input'     => $request ? $this->sanitize($request->except(['_token'])) : null,
            ],
        ]);
    }

    /** Ambil id mitra dari konteks aktif atau dari user. */
    protected function resolveMitraId($user): ?int
    {
        if (app()->bound('active_mitra')) {
            $mitra = app('active_mitra');

            if ($mitra) {
                return (int) $mitra->id;
            }
        }

        return $user?->mitra_id ? (int) $user->mitra_id : null;
    }

    /** Perkiraan modul dari nama rute admin (mis. admin.brand.index => brand). */
    protected function guessModule(?Request $request): ?string
    {
        $name = $request?->route()?->getName();

        if (! $name || ! str_starts_with($name, 'admin.')) {
            return null;
        }

        $parts = explode('.', $name);

        return $parts[1] ?? null;
    }

    /** Bersihkan data rahasia & potong nilai yang terlalu panjang. */
    protected function sanitize(?array $data): ?array
    {
        if (! $data) {
            return null;
        }

        $clean = [];

        foreach ($data as $key => $value) {
            if (in_array(strtolower((string) $key), self::SENSITIVE_KEYS, true)) {
                $clean[$key] = '***';
                continue;
            }

            if (is_array($value)) {
                $clean[$key] = $this->sanitize($value);
                continue;
            }

            if (is_string($value) && mb_strlen($value) > self::MAX_VALUE_LENGTH) {
                $value = mb_substr($value, 0, self::MAX_VALUE_LENGTH) . '…';
            }

            $clean[$key] = $value;
        }

        return $clean;
    }
}

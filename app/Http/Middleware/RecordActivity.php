<?php

namespace App\Http\Middleware;

use App\Services\AuditLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mencatat aktivitas user yang sudah login (navigasi halaman & aksi ubah data).
 *
 * Kejadian login/logout/percobaan login dicatat khusus di AuthController.
 * Permintaan AJAX GET (muat tabel Select2/DataTables) diabaikan agar catatan
 * tetap bermakna. Halaman Audit Trail sendiri tidak dicatat (menghindari banjir
 * catatan yang menunjuk ke dirinya sendiri).
 */
class RecordActivity
{
    /** Rute yang tidak dicatat di sini karena ditangani khusus. */
    protected const SKIP_ROUTES = [
        'logout', 'login.attempt',
    ];

    public function __construct(protected AuditLogger $audit)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            $this->record($request, $response);
        } catch (\Throwable $e) {
            // Pencatatan tidak boleh mengganggu respons.
        }

        return $response;
    }

    protected function record(Request $request, Response $response): void
    {
        if (! auth()->check()) {
            return;
        }

        $routeName = $request->route()?->getName();

        // Lewati rute khusus & halaman audit itu sendiri.
        if (in_array($routeName, self::SKIP_ROUTES, true) || str_starts_with((string) $routeName, 'admin.audit')) {
            return;
        }

        // Lewati AJAX GET (DataTables/Select2) — bukan aktivitas yang perlu dicatat.
        if ($request->ajax() && $request->isMethod('GET')) {
            return;
        }

        $event = $this->resolveEvent($request, $routeName);
        $path  = '/' . ltrim($request->path(), '/');

        $this->audit->log([
            'event'       => $event,
            'description' => strtoupper($request->method()) . ' ' . $path,
            'status_code' => $response->getStatusCode(),
            'properties'  => $this->payload($request),
        ]);
    }

    /** Tentukan jenis kejadian dari rute/metode HTTP. */
    protected function resolveEvent(Request $request, ?string $routeName): string
    {
        $name = (string) $routeName;

        if (str_ends_with($name, '.destroy') || $request->isMethod('DELETE')) {
            return 'delete';
        }
        if (str_ends_with($name, '.store')) {
            return 'create';
        }
        if (str_ends_with($name, '.update') || $request->isMethod('PUT') || $request->isMethod('PATCH')) {
            return 'update';
        }
        if ($request->isMethod('GET')) {
            return 'view';
        }

        return 'request';
    }

    /** Payload request untuk konteks tambahan (tanpa data rahasia). */
    protected function payload(Request $request): ?array
    {
        if ($request->isMethod('GET')) {
            return $request->query() ?: null;
        }

        return $request->except(['_token', '_method', 'password', 'password_confirmation']) ?: null;
    }
}

<?php

use App\Http\Middleware\RecordActivity;
use App\Http\Middleware\ResolveTenant;
use App\Http\Middleware\SetLocale;
use App\Services\AuditLogger;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SetLocale::class,
            RecordActivity::class,
        ]);

        $middleware->alias([
            'tenant' => ResolveTenant::class,
            'locale' => SetLocale::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Catat error/bug ke jejak audit. Exception yang "diharapkan"
        // (validasi, autentikasi, otorisasi, 404) tidak dicatat agar
        // catatan fokus pada gangguan nyata.
        $exceptions->report(function (Throwable $e): void {
            $ignored = [
                ValidationException::class,
                AuthenticationException::class,
                AuthorizationException::class,
                ModelNotFoundException::class,
                NotFoundHttpException::class,
            ];

            foreach ($ignored as $class) {
                if ($e instanceof $class) {
                    return;
                }
            }

            app(AuditLogger::class)->logError($e);
        });
    })->create();

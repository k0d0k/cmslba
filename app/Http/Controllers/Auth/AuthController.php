<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Modules\User\Models\User;
use Modules\Audit\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(protected AuditLogger $audit)
    {
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    /** Halaman pemberitahuan masa berlangganan mitra habis. */
    public function showExpired()
    {
        return view('auth.expired');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'string'],
            'password' => ['required'],
        ]);

        $field = filter_var($credentials['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'slug';

        if (! Auth::attempt([$field => $credentials['email'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $this->audit->log([
                'event'       => 'login_failed',
                'description' => __('audit.desc_login_failed', ['email' => $credentials['email']]),
                'user_email'  => $credentials['email'],
                'status_code' => 422,
            ]);

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();
        $user->forceFill(['last_login_at' => now()])->save();

        $this->audit->log([
            'event'       => 'login',
            'description' => __('audit.desc_login', ['email' => $user->email]),
            'user'        => $user,
            'status_code' => 200,
        ]);

        if ($user->locale) {
            $request->session()->put('locale', $user->locale);
        }
        if ($user->skin) {
            $request->session()->put('skin', $user->skin);
        }

        // User mitra hanya boleh masuk jika masa berlangganannya masih berlaku.
        // Principal (level pusat) selalu bisa login.
        if (! $user->isPrincipal() && $user->mitra_id) {
            $mitra = $user->mitra()->with('subscriptions')->first();
            if (! $mitra || ! $mitra->hasActiveSubscription()) {
                $this->audit->log([
                    'event'       => 'logout',
                    'description' => __('audit.desc_login_rejected', ['email' => $user->email]),
                    'user'        => $user,
                    'status_code' => 302,
                ]);

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login.expired');
            }
        }

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $this->audit->log([
                'event'       => 'logout',
                'description' => __('audit.desc_logout', ['email' => $user->email]),
                'user'        => $user,
                'status_code' => 302,
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

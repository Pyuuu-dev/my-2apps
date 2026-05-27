<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Services\TurnstileVerifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(LoginRequest $request, TurnstileVerifier $turnstile)
    {
        $username = (string) $request->input('username', '');
        $ipKey = 'login-ip:' . sha1((string) $request->ip());
        $userKey = 'login-user:' . sha1(mb_strtolower($username));

        // Per-IP throttle: 5 attempts / 5 minutes
        if (RateLimiter::tooManyAttempts($ipKey, 5)) {
            $seconds = RateLimiter::availableIn($ipKey);
            return back()
                ->with('error', 'Terlalu banyak percobaan login. Coba lagi dalam ' . max(1, ceil($seconds / 60)) . ' menit.')
                ->withInput($request->only('username'));
        }

        // Per-username throttle: 10 attempts / 15 minutes (mitigates credential stuffing
        // against a single account from rotating IPs)
        if (RateLimiter::tooManyAttempts($userKey, 10)) {
            $seconds = RateLimiter::availableIn($userKey);
            return back()
                ->with('error', 'Akun ini sementara dikunci. Coba lagi dalam ' . max(1, ceil($seconds / 60)) . ' menit.')
                ->withInput($request->only('username'));
        }

        // Verify Turnstile CAPTCHA before any auth lookup
        $captchaOk = $turnstile->verify(
            $request->input('cf-turnstile-response'),
            $request->ip()
        );

        if (!$captchaOk) {
            RateLimiter::hit($ipKey, 300);
            Log::warning('Login captcha failed', [
                'username' => $username,
                'ip' => $request->ip(),
                'ua' => substr((string) $request->userAgent(), 0, 200),
            ]);
            return back()
                ->with('error', 'Verifikasi keamanan gagal. Silakan coba lagi.')
                ->withInput($request->only('username'));
        }

        $credentials = [
            'username' => $username,
            'password' => (string) $request->input('password', ''),
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($ipKey);
            RateLimiter::clear($userKey);
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        // Failed credentials path
        RateLimiter::hit($ipKey, 300);     // 5 minutes
        RateLimiter::hit($userKey, 900);   // 15 minutes

        // Small jitter to mitigate timing-based user enumeration
        usleep(random_int(100_000, 400_000));

        Log::warning('Login failed', [
            'username' => $username,
            'ip' => $request->ip(),
            'ua' => substr((string) $request->userAgent(), 0, 200),
        ]);

        return back()
            ->with('error', 'Kredensial tidak valid.')
            ->withInput($request->only('username'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function showSettings()
    {
        return view('auth.settings');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers(),
            ],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Password lama salah!');
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('sukses', 'Password berhasil diubah!');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . Auth::id(),
        ]);

        Auth::user()->update($request->only('name', 'username'));

        return back()->with('sukses', 'Profil berhasil diperbarui!');
    }

    public function manualBackup()
    {
        try {
            Artisan::call('backup:database');
            $output = Artisan::output();

            if (str_contains($output, 'berhasil')) {
                return back()->with('sukses', 'Backup berhasil dikirim ke Telegram!');
            }

            return back()->with('error', 'Gagal mengirim backup. Periksa konfigurasi Telegram.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}

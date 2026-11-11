<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

// Controller untuk login via Google OAuth (Gmail)
class GoogleAuthController extends Controller
{
    // Redirect ke halaman Google
    public function redirect(): RedirectResponse
    {
        if (empty(config('services.google.client_id')) || empty(config('services.google.client_secret'))) {
            return redirect()->route('login')->withErrors([
                'email' => 'Konfigurasi Google OAuth belum lengkap. Silakan lengkapi GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET di file .env.',
            ]);
        }

        return Socialite::driver('google')->stateless()->redirect();
    }

    // Callback setelah user memilih akun Google
    public function callback(): RedirectResponse
    {
        try {
            $driver = Socialite::driver('google')->stateless();

            // Set Guzzle client with verify => false untuk kompatibilitas SSL di Windows / localhost
            $driver->setHttpClient(new \GuzzleHttp\Client([
                'verify' => env('GOOGLE_SSL_VERIFY', false),
            ]));

            $googleUser = $driver->user();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Google OAuth Error: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return redirect()->route('login')->withErrors([
                'email' => 'Gagal melakukan login dengan Google: ' . ($e->getMessage() ?: class_basename($e)),
            ]);
        }

        // Cari user yang sudah terdaftar berdasarkan google_id atau email
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);
        } else {
            $user = User::create([
                'name' => $googleUser->getName() ?: explode('@', $googleUser->getEmail())[0],
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'email_verified_at' => now(),
                'password' => null,
            ]);
        }

        Auth::login($user, remember: true);

        request()->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }
}


<x-guest-layout>
    <div class="auth-wrap">
        <div class="auth-card">

            {{-- Brand Mark --}}
            <div class="auth-card__brand">
                <div class="brand-badge brand-badge--center">
                    <span class="brand-badge__mark brand-badge__mark--lg">DG</span>
                    <div>
                        <div class="brand-badge__name text-lg">DataGen Studio</div>
                        <div class="brand-badge__role">QA Synthetic Data Suite</div>
                    </div>
                </div>
            </div>

            {{-- Title & Instructions --}}
            <div class="auth-card__heading">
                <h1 class="auth-title">Masuk ke Workspace</h1>
                <p class="auth-subtitle">Gunakan akun Google / Gmail Anda untuk mengakses platform.</p>
            </div>

            {{-- Error / Status Alerts --}}
            @if (session('status'))
                <div class="banner banner--success" style="margin-bottom: 16px;">
                    <div class="banner__content">{{ session('status') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="banner banner--error" style="margin-bottom: 16px;">
                    <div class="banner__content">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Google OAuth SSO Button --}}
            <div class="auth-sso" style="margin-bottom: 8px;">
                <a href="{{ route('auth.google') }}" class="btn btn--google btn--block btn--lg">
                    <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17Z"/>
                        <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24Z"/>
                        <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15Z"/>
                        <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98Z"/>
                    </svg>
                    <span>Masuk dengan Google</span>
                </a>
            </div>

            {{-- Fallback Manual Login (Collapsible) --}}
            <details class="auth-manual">
                <summary class="auth-manual__trigger">Atau login dengan email &amp; password &darr;</summary>
                <form method="POST" action="{{ route('login') }}" class="auth-manual__body">
                    @csrf
                    <input type="hidden" name="remember" value="1">

                    <div class="form-group">
                        <label for="email" class="input-label">Email</label>
                        <input id="email"
                               type="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autocomplete="username"
                               placeholder="nama@gmail.com"
                               class="text-input">
                    </div>

                    <div class="form-group">
                        <label for="password" class="input-label">Password</label>
                        <input id="password"
                               type="password"
                               name="password"
                               required
                               autocomplete="current-password"
                               placeholder="••••••••"
                               class="text-input">
                    </div>

                    <button type="submit" class="btn btn--outline btn--block">
                        Masuk dengan Email
                    </button>
                </form>
            </details>

        </div>
    </div>
</x-guest-layout>

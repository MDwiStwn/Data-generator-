<x-app-layout>
    <div class="profile-scroll-canvas" style="width: 100%; max-width: 100%;">
        {{-- Canvas Header --}}
        <div class="canvas-header" style="padding-bottom: 16px; border-bottom: 1px solid var(--rule); margin-bottom: 24px;">
            <div>
                <h1 class="canvas-title">Pengaturan Profil</h1>
                <p class="canvas-desc">Kelola informasi nama, alamat email, kata sandi, dan akun Anda.</p>
            </div>
            <div>
                <a href="{{ route('generator') }}" class="btn btn--subtle btn--sm" style="display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    <span>Kembali ke Generator</span>
                </a>
            </div>
        </div>

        <div class="max-w-4xl space-y-6" style="padding-bottom: 40px;">
            <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'DataGen Studio') }}</title>
        <meta name="description" content="Studio Generator Data Sintetis Pengujian Dapodik untuk QA dan Pengembang">

        <!-- Fonts: IBM Plex Sans & IBM Plex Mono -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:ital,wght@0,400;0,500;0,600;1,400&family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">

        <!-- Vite Assets -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body x-data="{ sidebarOpen: false }">
        <div class="app-frame">

            {{-- Mobile App Bar --}}
            <header class="app-mobile-bar">
                <div class="brand-badge">
                    <span class="brand-badge__mark">DG</span>
                    <span class="brand-badge__name">DataGen Studio</span>
                </div>
                <button type="button"
                        class="icon-button"
                        @click="sidebarOpen = !sidebarOpen"
                        aria-label="Buka Menu Navigasi">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
            </header>

            {{-- Mobile Backdrop --}}
            <div class="sidebar-backdrop"
                 :class="{ 'is-visible': sidebarOpen }"
                 @click="sidebarOpen = false"></div>

            {{-- Navigation Sidebar --}}
            <aside class="app-sidebar" :class="{ 'is-open': sidebarOpen }">
                {{-- Header / Logo --}}
                <div class="sidebar-header">
                    <div class="brand-badge">
                        <span class="brand-badge__mark">DG</span>
                        <div>
                            <div class="brand-badge__name">DataGen Studio</div>
                            <div class="brand-badge__role">QA & Test Suite</div>
                        </div>
                    </div>
                </div>

                {{-- Navigation Links --}}
                <nav class="sidebar-nav">
                    <div class="nav-section-label">Workspace</div>

                    <a href="{{ route('generator', ['tab' => 'generator']) }}"
                       class="nav-link {{ request()->get('tab', 'generator') === 'generator' ? 'nav-link--active' : '' }}"
                       @click="sidebarOpen = false">
                        <svg class="nav-link__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                            <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                            <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                            <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                            <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                        </svg>
                        <span>Generator Dataset</span>
                    </a>

                    <a href="{{ route('generator', ['tab' => 'history']) }}"
                       class="nav-link {{ request()->get('tab') === 'history' ? 'nav-link--active' : '' }}"
                       @click="sidebarOpen = false">
                        <svg class="nav-link__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                            <circle cx="12" cy="12" r="9"/>
                            <polyline points="12 7 12 12 15 15"/>
                        </svg>
                        <span>Riwayat Export</span>
                    </a>
                </nav>

                {{-- Status Meta --}}
                <div class="sidebar-meta">
                    <div class="meta-row">
                        <span class="meta-dot"></span>
                        <span class="meta-text">PHP {{ PHP_VERSION }} • Laravel {{ app()->version() }}</span>
                    </div>
                </div>

                {{-- User Profile & Logout --}}
                <div class="sidebar-footer">
                    <div class="user-block">
                        <div class="user-avatar" style="overflow: hidden;">
                            @if(!empty(Auth::user()->avatar))
                                <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            @else
                                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                            @endif
                        </div>
                        <div class="user-info">
                            <div class="user-name">{{ Auth::user()->name ?? 'User' }}</div>
                            <div class="user-role">{{ Auth::user()->email ?? '' }}</div>
                        </div>
                    </div>

                    <div class="user-actions">
                        <a href="{{ route('profile.edit') }}" class="action-link" title="Pengaturan Profil">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                            </svg>
                            <span>Profil</span>
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="action-link action-link--danger" title="Keluar dari sesi">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                    <polyline points="16 17 21 12 16 7"/>
                                    <line x1="21" y1="12" x2="9" y2="12"/>
                                </svg>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            {{-- Main Content Canvas: Framed Sheet --}}
            <main class="app-sheet">
                {{ $slot }}
            </main>

        </div>
        @livewireScripts
    </body>
</html>

<div class="history-flow">

    {{-- Alert --}}
    @if($flashMessage)
        <div class="banner {{ $flashType === 'success' ? 'banner--success' : 'banner--error' }}" role="alert">
            <div class="banner__icon">
                @if($flashType === 'success')
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                @else
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                @endif
            </div>
            <div class="banner__content">{{ $flashMessage }}</div>
            <button type="button" wire:click="$set('flashMessage', '')" class="banner__dismiss" aria-label="Tutup notifikasi">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    @endif

    @if(count($this->histories) > 0)
        <div class="table-card">
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="cell-id">ID</th>
                            <th>Template</th>
                            <th class="cell-num">Jumlah Baris</th>
                            <th>Karakteristik Mode</th>
                            <th>Waktu Generate</th>
                            <th>Operator</th>
                            <th class="cell-actions">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->histories as $history)
                            <tr>
                                <td class="cell-id">#{{ $history->id }}</td>
                                <td class="font-medium text-dark">{{ $history->category_label }}</td>
                                <td class="cell-num font-mono">{{ number_format($history->quantity, 0, ',', '.') }}</td>
                                <td>
                                    <span class="status-tag {{ $history->mode === 'edge_cases' ? 'status-tag--warning' : 'status-tag--neutral' }}">
                                        {{ $history->mode === 'edge_cases' ? 'Edge Cases' : 'Normal' }}
                                    </span>
                                </td>
                                <td class="text-muted font-mono text-xs">{{ $history->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-muted text-xs">{{ $history->user?->name ?? 'admin' }}</td>
                                <td class="cell-actions">
                                    <div class="actions-group">
                                        <button wire:click="rerunFromHistory({{ $history->id }})"
                                                class="btn btn--subtle btn--xs"
                                                title="Terapkan kembali parameter ini">
                                            Muat Ulang
                                        </button>
                                        <button wire:click="deleteHistory({{ $history->id }})"
                                                wire:confirm="Hapus catatan riwayat ini?"
                                                class="btn btn--danger-outline btn--xs"
                                                title="Hapus riwayat">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="table-card">
            <div class="preview-empty">
                <div class="empty-graphic">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.25">
                        <circle cx="12" cy="12" r="9"/>
                        <polyline points="12 7 12 12 15 15"/>
                    </svg>
                </div>
                <h3 class="empty-title">Belum Ada Riwayat Generate</h3>
                <p class="empty-desc">
                    Setiap sesi pembuatan dataset yang berhasil diexport akan otomatis tercatat di halaman ini beserta parameter konfigurasinya.
                </p>
                <div style="margin-top: 16px;">
                    <button wire:click="switchTab('generator')" class="btn btn--primary btn--sm">
                        Buka Generator
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>

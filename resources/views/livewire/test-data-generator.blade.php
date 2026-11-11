<div class="framed-canvas">
    {{-- Top Header / Action Row --}}
    <div class="canvas-header">
        <div>
            <h1 class="canvas-title">
                @if($activeTab === 'generator')
                    Generator Dataset
                @else
                    Riwayat Export
                @endif
            </h1>
            <p class="canvas-desc">
                @if($activeTab === 'generator')
                    Peralatan sintesis data pengujian format Dapodik (Pegawai, Murid, Tagihan SPP)
                @else
                    Daftar seluruh file dataset yang pernah dibuat dan diunduh pada sesi ini
                @endif
            </p>
        </div>

        @if($activeTab === 'history' && count($this->histories) > 0)
            <div>
                <button wire:click="clearAllHistories"
                        wire:confirm="Yakin ingin membersihkan seluruh riwayat export?"
                        class="btn btn--danger-outline btn--sm">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    </svg>
                    Bersihkan Riwayat
                </button>
            </div>
        @endif
    </div>

    {{-- Tab Views --}}
    <div class="canvas-body">
        @if($activeTab === 'generator')
            @include('livewire.partials.generator-tab')
        @elseif($activeTab === 'history')
            @include('livewire.partials.history-tab')
        @endif
    </div>
</div>

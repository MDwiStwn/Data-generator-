<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-4 border-b border-gray-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex flex-wrap gap-2">
            <button wire:click="switchTab('generator')"
                    class="px-4 py-2 text-sm font-medium rounded-md transition {{ $activeTab === 'generator' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Generator Data
            </button>
            <button wire:click="switchTab('history')"
                    class="px-4 py-2 text-sm font-medium rounded-md transition flex items-center gap-2 {{ $activeTab === 'history' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <span>Riwayat Export</span>
                @if(count($this->histories) > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $activeTab === 'history' ? 'bg-gray-700 text-white' : 'bg-gray-200 text-gray-800' }}">
                        {{ count($this->histories) }}
                    </span>
                @endif
            </button>
            <button wire:click="switchTab('guide')"
                    class="px-4 py-2 text-sm font-medium rounded-md transition {{ $activeTab === 'guide' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Panduan Pembuatan
            </button>
        </div>

        <div class="text-xs text-gray-500">
            Status: <span class="font-semibold text-green-600">Siap</span>
        </div>
    </div>
</div>

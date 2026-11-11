<div class="generator-flow">

    {{-- System Alert / Flash Notification --}}
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

    {{-- Main Generator Grid: Options & Preview --}}
    <div class="config-layout">

        {{-- LEFT COLUMN: Configuration Form --}}
        <div class="config-panel">

            {{-- Step 1: Pilih Template Schema --}}
            <section class="config-section">
                <div class="section-head">
                    <h2 class="section-head__title">1. Pilih Template</h2>
                    <button type="button"
                            wire:click="openCreateTemplateModal"
                            class="btn-text-action"
                            title="Buat atau upload template kustom baru">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>Template Baru</span>
                    </button>
                </div>

                <div class="template-selector">
                    {{-- Pegawai (Built-in Dapodik) --}}
                    <div wire:click="selectCategory('pegawai')"
                         class="template-card {{ $category === 'pegawai' ? 'template-card--selected' : '' }}">
                        <div class="template-card__header">
                            <span class="template-card__name">Pegawai</span>
                            <span class="template-card__tag">36 Kolom</span>
                        </div>
                        <p class="template-card__desc">Tenaga pendidik, GTK, NIK valid, NUPTK, NPWP, gelar, dan riwayat tugas.</p>
                    </div>

                    {{-- Peserta Didik (Built-in Dapodik) --}}
                    <div wire:click="selectCategory('murid')"
                         class="template-card {{ $category === 'murid' ? 'template-card--selected' : '' }}">
                        <div class="template-card__header">
                            <span class="template-card__name">Peserta Didik</span>
                            <span class="template-card__tag">37 Kolom</span>
                        </div>
                        <p class="template-card__desc">Data siswa, NISN, NIK, rombel, orang tua/wali, dan alamat Indonesia.</p>
                    </div>

                    {{-- Tagihan Siswa (Built-in Dapodik) --}}
                    <div wire:click="selectCategory('tagihan')"
                         class="template-card {{ $category === 'tagihan' ? 'template-card--selected' : '' }}">
                        <div class="template-card__header">
                            <span class="template-card__name">Tagihan Siswa</span>
                            <span class="template-card__tag">15 Kolom</span>
                        </div>
                        <p class="template-card__desc">Nomor virtual account, invoice SPP bulanan, nominal, dan status bayar.</p>
                    </div>

                    {{-- Custom Templates List --}}
                    @if(count($this->customTemplates) > 0)
                        <div class="custom-templates-divider">
                            <span>Template Kustom</span>
                        </div>

                        @foreach($this->customTemplates as $ct)
                            <div wire:click="selectCategory('custom_{{ $ct->id }}')"
                                 class="template-card template-card--custom {{ $category === 'custom_' . $ct->id ? 'template-card--selected' : '' }}">
                                <div class="template-card__header">
                                    <div class="flex-align-center gap-1">
                                        <span class="custom-badge">Kustom</span>
                                        <span class="template-card__name">{{ $ct->name }}</span>
                                    </div>
                                    <div class="flex-align-center gap-1">
                                        <span class="template-card__tag">{{ count($ct->columns ?? []) }} Kolom</span>
                                        <button type="button"
                                                wire:click.stop="deleteCustomTemplate({{ $ct->id }})"
                                                wire:confirm="Yakin ingin menghapus template '{{ $ct->name }}'?"
                                                class="delete-template-btn"
                                                title="Hapus template kustom ini">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <p class="template-card__desc">
                                    {{ $ct->description ?: 'Template kustom dengan ' . count($ct->columns ?? []) . ' field data yang dapat disintesis otomatis.' }}
                                </p>
                            </div>
                        @endforeach
                    @endif
                </div>

                @error('category')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </section>

            {{-- Step 2: Parameter Dataset --}}
            <section class="config-section">
                <div class="section-head">
                    <h2 class="section-head__title">2. Parameter Jumlah</h2>
                    <span class="section-head__hint">Maks. 10.000 baris</span>
                </div>

                {{-- Presets & Manual Input --}}
                <div class="quantity-control">
                    <div class="preset-group">
                        @foreach([10, 50, 100, 500, 1000] as $preset)
                            <button type="button"
                                    wire:click="setQuantity({{ $preset }})"
                                    class="preset-btn {{ (int)$quantity === $preset ? 'preset-btn--active' : '' }}">
                                {{ number_format($preset, 0, ',', '.') }}
                            </button>
                        @endforeach
                    </div>

                    <div class="quantity-input-wrap">
                        <label for="customQuantity" class="input-label">Jumlah custom</label>
                        <input id="customQuantity"
                               type="number"
                               wire:model.live.debounce.300ms="quantity"
                               min="1"
                               max="10000"
                               placeholder="Contoh: 250"
                               class="text-input text-input--mono">
                    </div>
                </div>

                @error('quantity')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </section>

            {{-- Step 3: Karakteristik Data & Mode --}}
            <section class="config-section">
                <div class="section-head">
                    <h2 class="section-head__title">3. Karakteristik Data</h2>
                    <span class="section-head__hint">Uji validasi QA</span>
                </div>

                <div class="mode-selector">
                    <label class="mode-card {{ $mode === 'normal' ? 'mode-card--active' : '' }}">
                        <input type="radio" wire:model.live="mode" value="normal" class="sr-only">
                        <div class="mode-card__body">
                            <span class="mode-card__title">Normal Dataset</span>
                            <span class="mode-card__desc">Data valid 100%, format standar, nama realistis Indonesia, siap import.</span>
                        </div>
                    </label>

                    <label class="mode-card {{ $mode === 'edge_cases' ? 'mode-card--active' : '' }}">
                        <input type="radio" wire:model.live="mode" value="edge_cases" class="sr-only">
                        <div class="mode-card__body">
                            <span class="mode-card__title">Edge Cases & Stresstest</span>
                            <span class="mode-card__desc">Karakter khusus, nama panjang, nilai null opsional, untuk uji ketahanan parsing.</span>
                        </div>
                    </label>
                </div>

                @error('mode')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </section>

            {{-- Step 4: Konfigurasi Periode (Khusus Tagihan) --}}
            @if($category === 'tagihan')
                <section class="config-section">
                    <div class="section-head">
                        <h2 class="section-head__title">4. Pengaturan Tagihan</h2>
                        <span class="section-head__hint">Tahun & Format</span>
                    </div>

                    <div class="field-grid">
                        <div>
                            <label for="tahunAjaran" class="input-label">Tahun Ajaran</label>
                            <select id="tahunAjaran" wire:model.live="tahunAjaran" class="select-input">
                                @foreach($this->tahunAjaranList as $ta)
                                    <option value="{{ $ta }}">{{ $ta }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="formatPeriode" class="input-label">Format Penulisan</label>
                            <select id="formatPeriode" wire:model.live="formatPeriode" class="select-input">
                                <option value="no_space">Tanpa Spasi (2025/2026)</option>
                                <option value="with_space">Dengan Spasi (2025 / 2026)</option>
                            </select>
                        </div>
                    </div>
                </section>
            @endif

            {{-- Generate Action Button --}}
            <div class="config-action">
                <button type="button"
                        wire:click="generate"
                        wire:loading.attr="disabled"
                        @if(empty($category) || (int)$quantity < 1) disabled @endif
                        class="btn btn--primary btn--block">
                    <span wire:loading.remove wire:target="generate">
                        Generate {{ number_format((int)$quantity, 0, ',', '.') }} Baris Data
                    </span>
                    <span wire:loading wire:target="generate" class="btn-loader">
                        <svg class="spinner" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memproses Dataset Sintetis...
                    </span>
                </button>
            </div>

        </div>

        {{-- RIGHT COLUMN: Preview & Download Canvas --}}
        <div class="preview-panel">

            @if($hasData && count($previewData) > 0)
                {{-- Data Summary & Export Bar --}}
                <div class="preview-header">
                    <div class="preview-summary">
                        <div class="summary-chip">
                            <span class="summary-chip__label">Template:</span>
                            <span class="summary-chip__val">{{ $this->getCategoryDisplayName($category) }}</span>
                        </div>
                        <div class="summary-chip">
                            <span class="summary-chip__label">Total Baris:</span>
                            <span class="summary-chip__val font-mono">{{ number_format($totalRows, 0, ',', '.') }}</span>
                        </div>
                        <div class="summary-chip">
                            <span class="summary-chip__label">Kolom:</span>
                            <span class="summary-chip__val font-mono">{{ count($previewHeaders) }}</span>
                        </div>
                        <div class="summary-chip">
                            <span class="summary-chip__label">Mode:</span>
                            <span class="summary-chip__val">{{ $mode === 'edge_cases' ? 'Edge Cases' : 'Normal' }}</span>
                        </div>
                    </div>

                    {{-- Export Format Buttons --}}
                    <div class="export-actions">
                        <button wire:click="export('xlsx')" class="btn btn--outline btn--sm" title="Unduh format Excel XLSX">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            <span>XLSX</span>
                        </button>

                        <button wire:click="export('csv')" class="btn btn--outline btn--sm" title="Unduh format Comma-Separated Values">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            <span>CSV</span>
                        </button>

                        <button wire:click="export('sql')" class="btn btn--outline btn--sm" title="Unduh format SQL INSERT">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                            </svg>
                            <span>SQL</span>
                        </button>

                        <button wire:click="resetData" class="btn btn--subtle btn--sm" title="Reset dataset">
                            Reset
                        </button>
                    </div>
                </div>

                {{-- Table View --}}
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="cell-num">#</th>
                                @foreach($previewHeaders as $h)
                                    <th>{{ $h }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($previewData as $idx => $row)
                                <tr>
                                    <td class="cell-num">{{ $idx + 1 }}</td>
                                    @foreach($previewKeys as $k)
                                        <td class="cell-data">{{ $row[$k] ?? '-' }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="table-footer">
                    @if($totalRows > count($previewData))
                        Menampilkan preview {{ count($previewData) }} baris pertama dari total <strong>{{ number_format($totalRows, 0, ',', '.') }}</strong> baris dataset.
                    @else
                        Menampilkan seluruh <strong>{{ count($previewData) }}</strong> baris dataset.
                    @endif
                </div>

            @else
                {{-- Clean Empty State --}}
                <div class="preview-empty">
                    <div class="empty-graphic">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.25">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="3" y1="9" x2="21" y2="9"></line>
                            <line x1="3" y1="15" x2="21" y2="15"></line>
                            <line x1="9" y1="3" x2="9" y2="21"></line>
                            <line x1="15" y1="3" x2="15" y2="21"></line>
                        </svg>
                    </div>
                    <h3 class="empty-title">Belum Ada Dataset yang Digenerate</h3>
                    <p class="empty-desc">
                        Pilih template dataset di kolom sebelah kiri (bawaan Dapodik atau template kustom Anda), tentukan kuantitas baris, lalu klik <strong>Generate</strong>.
                    </p>

                    <div class="schema-hints">
                        <div class="hint-card">
                            <span class="hint-title">Pegawai Dapodik</span>
                            <span class="hint-meta">36 Kolom • NIK, NUPTK, NPWP, Golongan, Tugas</span>
                        </div>
                        <div class="hint-card">
                            <span class="hint-title">Peserta Didik (Siswa)</span>
                            <span class="hint-meta">37 Kolom • NISN, Rombel, Alamat, Wali</span>
                        </div>
                        <div class="hint-card">
                            <span class="hint-title">Tagihan Siswa (SPP)</span>
                            <span class="hint-meta">15 Kolom • Virtual Account, Nominal, Status</span>
                        </div>
                    </div>
                </div>
            @endif

        </div>

    </div>

    {{-- MODAL: Tambah Template Baru (Upload atau Buat Manual) --}}
    @if($showCreateTemplateModal)
        <div class="modal-backdrop">
            <div class="modal-card">
                {{-- Modal Header --}}
                <div class="modal-header">
                    <div>
                        <h2 class="modal-title">Tambah Template Dataset Baru</h2>
                        <p class="modal-subtitle">Upload file template (Excel/CSV) atau susun kolom data secara manual.</p>
                    </div>
                    <button type="button" wire:click="closeCreateTemplateModal" class="modal-close-btn" aria-label="Tutup modal">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="18" x2="18" y2="6"></line>
                        </svg>
                    </button>
                </div>

                {{-- Method Switcher (Alur 1: Upload, Alur 3: Manual) --}}
                <div class="modal-method-tabs">
                    <button type="button"
                            wire:click="setCreateMethod('upload')"
                            class="method-tab {{ $templateCreateMethod === 'upload' ? 'method-tab--active' : '' }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        <span>Upload File (Excel / CSV)</span>
                    </button>

                    <button type="button"
                            wire:click="setCreateMethod('manual')"
                            class="method-tab {{ $templateCreateMethod === 'manual' ? 'method-tab--active' : '' }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                            <path d="M12 20h9"></path>
                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                        </svg>
                        <span>Buat Manual (Input Kolom)</span>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="modal-body">

                    {{-- ALUR 1: UPLOAD FILE --}}
                    @if($templateCreateMethod === 'upload')
                        <div class="upload-dropzone" x-data="{ isDragging: false }">
                            <input type="file"
                                   id="templateFileInput"
                                   wire:model="newTemplateFile"
                                   accept=".xlsx,.xls,.csv"
                                   class="sr-only">
                            <label for="templateFileInput" class="dropzone-label">
                                <div class="dropzone-icon">
                                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                        <line x1="12" y1="18" x2="12" y2="12"></line>
                                        <line x1="9" y1="15" x2="15" y2="15"></line>
                                    </svg>
                                </div>
                                <div class="dropzone-text">
                                    <span class="dropzone-action">Klik untuk memilih file template</span> atau seret file ke sini
                                </div>
                                <div class="dropzone-hint">
                                    Mendukung format <strong>.xlsx</strong>, <strong>.xls</strong>, atau <strong>.csv</strong> (Maks. 10MB)
                                </div>
                            </label>

                            <div wire:loading wire:target="newTemplateFile" class="upload-loading-indicator">
                                <svg class="spinner" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Membaca header kolom dan mendeteksi tipe data...</span>
                            </div>
                        </div>

                        @error('newTemplateFile')
                            <div class="field-error" style="margin-top: 8px;">{{ $message }}</div>
                        @enderror
                    @endif

                    {{-- Common Metadata: Nama & Deskripsi Template --}}
                    <div class="form-grid" style="margin-top: 16px;">
                        <div>
                            <label for="newTemplateName" class="input-label">Nama Template *</label>
                            <input id="newTemplateName"
                                   type="text"
                                   wire:model="newTemplateName"
                                   placeholder="Contoh: Template Alumni 2026"
                                   class="text-input">
                            @error('newTemplateName')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="newTemplateDescription" class="input-label">Deskripsi Singkat (Opsional)</label>
                            <input id="newTemplateDescription"
                                   type="text"
                                   wire:model="newTemplateDescription"
                                   placeholder="Contoh: Format data tracer study alumni"
                                   class="text-input">
                        </div>
                    </div>

                    {{-- Column Review / Configuration Editor --}}
                    <div class="column-editor-section" style="margin-top: 20px;">
                        <div class="section-head">
                            <div>
                                <h3 class="section-head__title" style="font-size: 13px;">Daftar Kolom ({{ count($newTemplateColumns) }})</h3>
                                <p class="section-head__hint">Tentukan tipe data sintetis yang dihasilkan untuk setiap kolom.</p>
                            </div>
                            <button type="button" wire:click="addColumn" class="btn btn--subtle btn--xs">
                                + Tambah Kolom
                            </button>
                        </div>

                        @if(empty($newTemplateColumns))
                            <div class="empty-columns-hint">
                                @if($templateCreateMethod === 'upload')
                                    Upload file Excel/CSV di atas untuk mendeteksi kolom otomatis, atau klik "+ Tambah Kolom".
                                @else
                                    Klik "+ Tambah Kolom" di atas untuk menambahkan field pertama.
                                @endif
                            </div>
                        @else
                            <div class="column-list">
                                <div class="column-list-header">
                                    <span style="flex: 1;">Nama Header Kolom</span>
                                    <span style="width: 200px;">Tipe Data Sintetis</span>
                                    <span style="width: 36px; text-align: center;">Aksi</span>
                                </div>

                                @foreach($newTemplateColumns as $index => $col)
                                    <div class="column-row" wire:key="col-row-{{ $index }}">
                                        <div style="flex: 1;">
                                            <input type="text"
                                                   wire:model="newTemplateColumns.{{ $index }}.header"
                                                   placeholder="Nama kolom di Excel"
                                                   class="text-input text-input--sm">
                                            @error("newTemplateColumns.{$index}.header")
                                                <div class="field-error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div style="width: 200px;">
                                            <select wire:model="newTemplateColumns.{{ $index }}.type" class="select-input select-input--sm">
                                                @foreach($this->supportedFieldTypes as $typeKey => $typeLabel)
                                                    <option value="{{ $typeKey }}">{{ $typeLabel }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div style="width: 36px; text-align: center;">
                                            <button type="button"
                                                    wire:click="removeColumn({{ $index }})"
                                                    class="btn-icon-danger"
                                                    title="Hapus kolom ini">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                                    <line x1="6" y1="18" x2="18" y2="6"></line>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @error('newTemplateColumns')
                            <div class="field-error" style="margin-top: 8px;">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer">
                    <button type="button" wire:click="closeCreateTemplateModal" class="btn btn--outline btn--sm">
                        Batal
                    </button>
                    <button type="button"
                            wire:click="saveCustomTemplate"
                            @if(empty($newTemplateColumns)) disabled @endif
                            class="btn btn--primary btn--sm">
                        Simpan & Gunakan Template
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>

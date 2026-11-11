<?php

namespace App\Livewire;

use App\Enums\GenerateMode;
use App\Models\CustomTemplate;
use App\Models\GenerationHistory;
use App\Services\ExportService;
use App\Services\TemplateDetectionService;
use App\Services\TestDataGeneratorService;
use App\Support\TemplateSchema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;

// Component Livewire untuk halaman Generator Data Sintetis Dapodik
#[Layout('layouts.app')]
#[Title('Test Data Generator')]
class TestDataGenerator extends Component
{
    use WithFileUploads;

    #[Url(as: 'tab')]
    public string $activeTab = 'generator';

    public string $category = '';
    public int|string $quantity = 100;
    public string $mode = 'normal';
    public string $tahunAjaran = '2021/2022';
    public string $periodePembukuan = '2021/2022';
    public string $formatPeriode = 'no_space'; // 'no_space' or 'with_space'

    public bool $isGenerating = false;
    public bool $hasData = false;
    public string $flashMessage = '';
    public string $flashType = '';

    // Preview data stored in component state
    public array $previewHeaders = [];
    public array $previewKeys = [];
    public array $previewData = [];
    public int $totalRows = 0;

    // --- Modal & Custom Template Builder State ---
    public bool $showCreateTemplateModal = false;
    public string $templateCreateMethod = 'upload'; // 'upload' or 'manual'
    public $newTemplateFile = null;
    public string $newTemplateName = '';
    public string $newTemplateDescription = '';
    public array $newTemplateColumns = [];
    public bool $isParsingTemplate = false;

    public function updatedTahunAjaran(string $val): void
    {
        $this->syncPeriodePembukuan();
    }

    public function updatedFormatPeriode(string $val): void
    {
        $this->syncPeriodePembukuan();
    }

    private function syncPeriodePembukuan(): void
    {
        $valClean = str_replace(' ', '', $this->tahunAjaran);
        if (str_contains($valClean, '/')) {
            $parts = explode('/', $valClean);
            if ($this->formatPeriode === 'with_space') {
                $this->periodePembukuan = "{$parts[0]} / {$parts[1]}";
            } else {
                $this->periodePembukuan = "{$parts[0]}/{$parts[1]}";
            }
        } else {
            $this->periodePembukuan = $this->tahunAjaran;
        }
    }

    public function getTahunAjaranListProperty(): array
    {
        return [
            '2021/2022',
            '2022/2023',
            '2023/2024',
            '2024/2025',
            '2025/2026',
            '2026/2027',
            '2027/2028',
            '2028/2029',
            '2029/2030',
            '2030/2031',
            '2031/2032',
        ];
    }

    public function getSupportedFieldTypesProperty(): array
    {
        return TemplateDetectionService::supportedTypes();
    }

    public function getCustomTemplatesProperty()
    {
        return CustomTemplate::where('user_id', auth()->id())->orderBy('name')->get();
    }

    protected function rules(): array
    {
        $validCategories = ['pegawai', 'murid', 'tagihan'];
        foreach ($this->customTemplates as $ct) {
            $validCategories[] = 'custom_' . $ct->id;
        }

        return [
            'category' => [
                'required',
                Rule::in($validCategories),
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:10000',
            ],
            'mode' => [
                'required',
                Rule::in(array_map(fn($m) => $m->value, GenerateMode::availableModes())),
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'category.required' => 'Pilih template terlebih dahulu.',
            'category.in' => 'Template yang dipilih tidak valid.',
            'quantity.required' => 'Jumlah data harus diisi.',
            'quantity.integer' => 'Jumlah data harus berupa angka.',
            'quantity.min' => 'Jumlah data minimal 1 baris.',
            'quantity.max' => 'Jumlah data maksimal 10.000 baris.',
            'mode.required' => 'Mode generate harus dipilih.',
            'mode.in' => 'Mode generate tidak valid.',
        ];
    }

    public function setQuantity(int $qty): void
    {
        $this->quantity = $qty;
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = in_array($tab, ['generator', 'history'], true) ? $tab : 'generator';
    }

    public function selectCategory(string $category): void
    {
        $this->category = $category;
        $this->resetData();
    }

    // --- Custom Template Modal & Creator Actions ---

    public function openCreateTemplateModal(): void
    {
        $this->resetCreateTemplateForm();
        $this->showCreateTemplateModal = true;
    }

    public function closeCreateTemplateModal(): void
    {
        $this->showCreateTemplateModal = false;
        $this->resetCreateTemplateForm();
    }

    public function setCreateMethod(string $method): void
    {
        $this->templateCreateMethod = in_array($method, ['upload', 'manual'], true) ? $method : 'upload';
        if ($this->templateCreateMethod === 'manual' && empty($this->newTemplateColumns)) {
            $this->addColumn();
            $this->addColumn();
            $this->addColumn();
        }
    }

    public function updatedNewTemplateFile(): void
    {
        $this->validate([
            'newTemplateFile' => 'required|file|max:10240|mimes:xlsx,xls,csv,txt',
        ], [
            'newTemplateFile.mimes' => 'Format file harus berupa Excel (.xlsx, .xls) atau CSV (.csv).',
            'newTemplateFile.max' => 'Ukuran file template maksimal 10MB.',
        ]);

        $this->isParsingTemplate = true;

        try {
            $detector = app(TemplateDetectionService::class);
            $columns = $detector->parseFile($this->newTemplateFile);

            if (empty($columns)) {
                $this->flashMessage = 'Tidak dapat mendeteksi kolom header dari file yang diupload. Pastikan baris pertama berisi nama kolom.';
                $this->flashType = 'error';
                return;
            }

            $this->newTemplateColumns = $columns;

            // Pre-fill template name from filename if empty
            if (empty($this->newTemplateName)) {
                $fileName = pathinfo($this->newTemplateFile->getClientOriginalName(), PATHINFO_FILENAME);
                $this->newTemplateName = ucwords(str_replace(['_', '-'], ' ', $fileName));
            }

            $this->flashMessage = 'Berhasil mendeteksi ' . count($columns) . ' kolom dari file template. Anda dapat menyesuaikan tipe data sebelum menyimpan.';
            $this->flashType = 'success';
        } catch (\Throwable $e) {
            $this->flashMessage = 'Gagal memproses file template: ' . $e->getMessage();
            $this->flashType = 'error';
        } finally {
            $this->isParsingTemplate = false;
        }
    }

    public function addColumn(): void
    {
        $count = count($this->newTemplateColumns) + 1;
        $this->newTemplateColumns[] = [
            'header'   => 'Kolom ' . $count,
            'key'      => 'kolom_' . $count,
            'type'     => 'text',
            'required' => true,
        ];
    }

    public function removeColumn(int $index): void
    {
        if (isset($this->newTemplateColumns[$index])) {
            unset($this->newTemplateColumns[$index]);
            $this->newTemplateColumns = array_values($this->newTemplateColumns);
        }
    }

    public function saveCustomTemplate(): void
    {
        $this->validate([
            'newTemplateName' => 'required|string|min:2|max:100',
            'newTemplateColumns' => 'required|array|min:1',
            'newTemplateColumns.*.header' => 'required|string|max:100',
            'newTemplateColumns.*.type' => 'required|string',
        ], [
            'newTemplateName.required' => 'Nama template harus diisi.',
            'newTemplateColumns.min' => 'Template harus memiliki minimal 1 kolom.',
            'newTemplateColumns.*.header.required' => 'Nama kolom tidak boleh kosong.',
        ]);

        // Clean & ensure unique keys for columns
        $cleanedColumns = [];
        $existingKeys = [];

        foreach ($this->newTemplateColumns as $idx => $col) {
            $header = trim((string) $col['header']);
            $key = Str::snake(preg_replace('/[^a-zA-Z0-9_]/', '', str_replace([' ', '-'], '_', strtolower($header))));
            if (empty($key)) {
                $key = 'col_' . ($idx + 1);
            }

            $baseKey = $key;
            $counter = 1;
            while (in_array($key, $existingKeys, true)) {
                $key = $baseKey . '_' . $counter++;
            }
            $existingKeys[] = $key;

            $cleanedColumns[] = [
                'header'   => $header,
                'key'      => $key,
                'type'     => $col['type'] ?? 'text',
                'required' => (bool) ($col['required'] ?? true),
            ];
        }

        $template = CustomTemplate::create([
            'user_id'     => auth()->id(),
            'name'        => trim($this->newTemplateName),
            'description' => trim($this->newTemplateDescription),
            'columns'     => $cleanedColumns,
        ]);

        $this->category = 'custom_' . $template->id;
        $this->closeCreateTemplateModal();

        $this->flashMessage = "Template kustom \"{$template->name}\" (" . count($cleanedColumns) . " kolom) berhasil dibuat dan siap digunakan!";
        $this->flashType = 'success';
    }

    public function deleteCustomTemplate(int $templateId): void
    {
        $template = CustomTemplate::where('user_id', auth()->id())->find($templateId);
        if ($template) {
            $name = $template->name;
            $template->delete();

            if ($this->category === 'custom_' . $templateId) {
                $this->category = 'pegawai';
                $this->resetData();
            }

            $this->flashMessage = "Template kustom \"{$name}\" berhasil dihapus.";
            $this->flashType = 'success';
        }
    }

    private function resetCreateTemplateForm(): void
    {
        $this->templateCreateMethod = 'upload';
        $this->newTemplateFile = null;
        $this->newTemplateName = '';
        $this->newTemplateDescription = '';
        $this->newTemplateColumns = [];
        $this->isParsingTemplate = false;
    }

    // --- Generation & Export Actions ---

    public function generate(): void
    {
        $this->validate();

        $this->isGenerating = true;

        try {
            $service = app(TestDataGeneratorService::class);
            $generateMode = GenerateMode::from($this->mode);
            $qty = (int) $this->quantity;

            $options = [];
            if ($this->category === 'tagihan') {
                $options['tahun_ajaran'] = $this->tahunAjaran;
                $options['periode_pembukuan'] = $this->periodePembukuan;
            }

            $result = $service->generate($this->category, $qty, $generateMode, $options);

            $this->previewHeaders = $result['headers'];
            $this->previewKeys = $result['keys'];
            $previewSlice = array_slice($result['data'], 0, 100);
            $this->previewData = $this->serializeData($previewSlice);
            $this->totalRows = count($result['data']);
            $this->hasData = true;

            // Save to GenerationHistory table
            GenerationHistory::create([
                'user_id' => auth()->id(),
                'category' => $this->category,
                'quantity' => $qty,
                'mode' => $this->mode,
            ]);

            // Store in session for export
            session([
                'generated_data' => [
                    'user_id' => auth()->id(),
                    'headers' => $result['headers'],
                    'keys' => $result['keys'],
                    'data' => $result['data'],
                    'category' => $this->category,
                ],
            ]);

            $categoryLabel = $this->getCategoryDisplayName($this->category);
            $this->flashMessage = "Berhasil generate {$this->totalRows} baris data {$categoryLabel} dan tersimpan di Riwayat Export.";
            $this->flashType = 'success';
        } catch (\Throwable $e) {
            $this->flashMessage = 'Terjadi kesalahan saat generate data: ' . $e->getMessage();
            $this->flashType = 'error';
        } finally {
            $this->isGenerating = false;
        }
    }

    public function getCategoryDisplayName(string $category): string
    {
        if (str_starts_with($category, 'custom_')) {
            $id = (int) substr($category, 7);
            $custom = CustomTemplate::find($id);
            return $custom ? $custom->name : 'Template Kustom';
        }

        return TemplateSchema::categories()[$category] ?? ucfirst($category);
    }

    public function rerunFromHistory(int $historyId): void
    {
        $history = GenerationHistory::where('user_id', auth()->id())->find($historyId);
        if (! $history) {
            return;
        }

        $this->category = $history->category;
        $this->quantity = $history->quantity;
        $this->mode = $history->mode;
        $this->activeTab = 'generator';

        $this->generate();
    }

    public function deleteHistory(int $historyId): void
    {
        $history = GenerationHistory::where('user_id', auth()->id())->find($historyId);
        if ($history) {
            $history->delete();
            $this->flashMessage = "Berhasil menghapus item riwayat #RUN-" . str_pad((string) $historyId, 4, '0', STR_PAD_LEFT) . ".";
            $this->flashType = 'success';
        }
    }

    public function clearAllHistories(): void
    {
        GenerationHistory::where('user_id', auth()->id())->delete();
        $this->flashMessage = 'Seluruh riwayat export Anda telah berhasil dibersihkan.';
        $this->flashType = 'success';
    }

    /**
     * Unified export handler supporting xlsx, csv, and sql.
     */
    public function export(string $format)
    {
        return match (strtolower($format)) {
            'xlsx', 'excel' => $this->exportExcel(),
            'csv'           => $this->exportCsv(),
            'sql'           => $this->exportSql(),
            default         => response()->noContent(),
        };
    }

    public function exportCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $sessionData = session('generated_data');

        if (! $sessionData || (isset($sessionData['user_id']) && $sessionData['user_id'] !== auth()->id())) {
            $this->flashMessage = 'Tidak ada data untuk diekspor. Generate data terlebih dahulu.';
            $this->flashType = 'error';
            return response()->streamDownload(fn() => null, 'error.csv');
        }

        $exportService = app(ExportService::class);

        return $exportService->exportCsv(
            $sessionData['category'],
            $sessionData['headers'],
            $sessionData['keys'],
            $sessionData['data']
        );
    }

    public function exportExcel(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $sessionData = session('generated_data');

        if (! $sessionData || (isset($sessionData['user_id']) && $sessionData['user_id'] !== auth()->id())) {
            $this->flashMessage = 'Tidak ada data untuk diekspor. Generate data terlebih dahulu.';
            $this->flashType = 'error';
            return response()->download(public_path('favicon.ico'));
        }

        $exportService = app(ExportService::class);

        return $exportService->exportExcel(
            $sessionData['category'],
            $sessionData['headers'],
            $sessionData['keys'],
            $sessionData['data']
        );
    }

    public function exportSql(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $sessionData = session('generated_data');

        if (! $sessionData || (isset($sessionData['user_id']) && $sessionData['user_id'] !== auth()->id())) {
            $this->flashMessage = 'Tidak ada data untuk diekspor. Generate data terlebih dahulu.';
            $this->flashType = 'error';
            return response()->streamDownload(fn() => null, 'error.sql');
        }

        $exportService = app(ExportService::class);

        return $exportService->exportSql(
            $sessionData['category'],
            $sessionData['headers'],
            $sessionData['keys'],
            $sessionData['data']
        );
    }

    public function resetData(): void
    {
        $this->hasData = false;
        $this->previewHeaders = [];
        $this->previewKeys = [];
        $this->previewData = [];
        $this->totalRows = 0;
        $this->flashMessage = '';
        session()->forget('generated_data');
    }

    public function getCategoriesProperty(): array
    {
        return TemplateSchema::categories();
    }

    public function getModesProperty(): array
    {
        return array_map(fn(GenerateMode $m) => [
            'value' => $m->value,
            'label' => $m->label(),
            'description' => $m->description(),
        ], GenerateMode::availableModes());
    }

    public function getHistoriesProperty()
    {
        return GenerationHistory::with('user')
            ->where('user_id', auth()->id())
            ->latest()
            ->take(50)
            ->get();
    }

    public function render()
    {
        return view('livewire.test-data-generator');
    }

    private function serializeData(array $data): array
    {
        return array_map(function (array $row) {
            return array_map(function ($value) {
                if ($value instanceof \DateTimeInterface) {
                    return $value->format('Y-m-d');
                }
                return $value;
            }, $row);
        }, $data);
    }
}

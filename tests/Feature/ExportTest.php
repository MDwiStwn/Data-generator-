<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\TestDataGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_guest_cannot_export(): void
    {
        $response = $this->post('/export/csv');
        $response->assertRedirect('/login');

        $response2 = $this->post('/export/excel');
        $response2->assertRedirect('/login');
    }

    public function test_export_csv_with_session_data(): void
    {
        $service = new TestDataGeneratorService();
        $result = $service->generate('tagihan', 5);

        session([
            'generated_data' => [
                'headers' => $result['headers'],
                'keys' => $result['keys'],
                'data' => $result['data'],
                'category' => 'tagihan',
            ],
        ]);

        $response = $this->actingAs($this->user)->post('/export/csv');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_export_excel_with_session_data(): void
    {
        $service = new TestDataGeneratorService();
        $result = $service->generate('pegawai', 5);

        session([
            'generated_data' => [
                'headers' => $result['headers'],
                'keys' => $result['keys'],
                'data' => $result['data'],
                'category' => 'pegawai',
            ],
        ]);

        $response = $this->actingAs($this->user)->post('/export/excel');
        $response->assertStatus(200);
    }

    public function test_export_excel_murid_starts_data_at_row_7(): void
    {
        $service = new TestDataGeneratorService();
        $result = $service->generate('murid', 3);

        session([
            'generated_data' => [
                'headers' => $result['headers'],
                'keys' => $result['keys'],
                'data' => $result['data'],
                'category' => 'murid',
            ],
        ]);

        $response = $this->actingAs($this->user)->post('/export/excel');
        $response->assertStatus(200);

        // Verify the structure with TestDataExport directly
        $export = new \App\Exports\TestDataExport(
            $result['headers'],
            $result['keys'],
            $result['data'],
            \App\Support\TemplateSchema::getSheetName('murid'),
            'murid'
        );

        $tempPath = storage_path('app/test_murid_verify.xlsx');
        \Maatwebsite\Excel\Facades\Excel::store($export, 'test_murid_verify.xlsx', 'local');

        $filePath = storage_path('app/private/test_murid_verify.xlsx');
        if (! file_exists($filePath)) {
            $filePath = storage_path('app/test_murid_verify.xlsx');
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Notes on rows 1-3
        $this->assertSame('Notes:', $sheet->getCell('A1')->getValue());
        $this->assertSame('Tanda (*) Wajib Diisi', $sheet->getCell('A2')->getValue());
        $this->assertSame('Kosongkan kolom jika tidak diisi', $sheet->getCell('A3')->getValue());

        // Header on row 5
        $this->assertSame('No', $sheet->getCell('A5')->getValue());
        $this->assertSame('Nama (*)', $sheet->getCell('B5')->getValue());
        $this->assertSame('Data Ayah', $sheet->getCell('AD5')->getValue());

        // Data begins at row 7
        $this->assertSame('1', (string) $sheet->getCell('A7')->getValue());
        $this->assertNotEmpty($sheet->getCell('B7')->getValue());

        // Clean up
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    public function test_export_excel_tagihan_dates_and_amount_are_numeric(): void
    {
        $service = new TestDataGeneratorService();
        $result = $service->generate('tagihan', 3);

        $export = new \App\Exports\TestDataExport(
            $result['headers'],
            $result['keys'],
            $result['data'],
            \App\Support\TemplateSchema::getSheetName('tagihan'),
            'tagihan'
        );

        \Maatwebsite\Excel\Facades\Excel::store($export, 'test_tagihan_verify.xlsx', 'local');

        $filePath = storage_path('app/private/test_tagihan_verify.xlsx');
        if (! file_exists($filePath)) {
            $filePath = storage_path('app/test_tagihan_verify.xlsx');
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Row 2 data
        $jumlahTagihan = $sheet->getCell('C2')->getValue();
        $tanggalMulai = $sheet->getCell('F2')->getValue();
        $tanggalJatuhTempo = $sheet->getCell('G2')->getValue();
        $bulan = $sheet->getCell('J2')->getValue();

        // Assert they are numeric, not strings!
        $this->assertIsInt($jumlahTagihan);
        $this->assertIsNumeric($tanggalMulai);
        $this->assertIsNumeric($tanggalJatuhTempo);
        $this->assertIsNumeric($bulan);

        // Assert target system's excelToDateTimeObject can parse them without floor() TypeError!
        $dtMulai = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggalMulai);
        $dtJatuhTempo = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggalJatuhTempo);
        $dtBulan = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($bulan);

        $this->assertInstanceOf(\DateTime::class, $dtMulai);
        $this->assertInstanceOf(\DateTime::class, $dtJatuhTempo);
        $this->assertInstanceOf(\DateTime::class, $dtBulan);

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}


<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

// Service untuk mendeteksi kolom dan tipe data saat upload template Excel / CSV
class TemplateDetectionService
{
    /**
     * Supported field types for custom templates.
     *
     * @return array<string, string>
     */
    public static function supportedTypes(): array
    {
        return [
            'name'        => 'Nama Lengkap (Indonesia)',
            'nik'         => 'NIK (16 Digit)',
            'nisn'        => 'NISN (10 Digit)',
            'nuptk'       => 'NUPTK (16 Digit)',
            'nip'         => 'NIP Pegawai (18 Digit)',
            'gender'      => 'Jenis Kelamin (L/P)',
            'birth_place' => 'Tempat Lahir (Kota)',
            'date'        => 'Tanggal (YYYY-MM-DD)',
            'email'       => 'Email',
            'phone'       => 'Nomor Telepon / HP',
            'address'     => 'Alamat Jalan',
            'city'        => 'Kota / Kabupaten',
            'province'    => 'Provinsi',
            'postal_code' => 'Kode Pos (5 Digit)',
            'currency'    => 'Nominal Uang (Rp)',
            'number'      => 'Angka / Nomor Urut',
            'boolean'     => 'Status (Aktif/Nonaktif)',
            'text'        => 'Teks Bebas / Keterangan',
        ];
    }

    /**
     * Parse uploaded template file (xlsx, xls, csv) and extract headers.
     *
     * @param UploadedFile|string $file
     * @return array<int, array{header: string, key: string, type: string, required: bool}>
     */
    public function parseFile(UploadedFile|string $file): array
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;
        $extension = $file instanceof UploadedFile 
            ? strtolower($file->getClientOriginalExtension()) 
            : strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        $headers = [];

        if ($extension === 'csv') {
            $headers = $this->parseCsvHeaders($filePath);
        } else {
            $headers = $this->parseExcelHeaders($filePath);
        }

        // Clean headers and auto-detect types
        $columns = [];
        $existingKeys = [];

        foreach ($headers as $index => $header) {
            $cleanHeader = trim((string) $header);
            if ($cleanHeader === '') {
                continue;
            }

            $key = Str::snake(preg_replace('/[^a-zA-Z0-9_]/', '', str_replace([' ', '-'], '_', strtolower($cleanHeader))));
            if (empty($key)) {
                $key = 'col_' . ($index + 1);
            }

            // Ensure unique key
            $baseKey = $key;
            $counter = 1;
            while (in_array($key, $existingKeys, true)) {
                $key = $baseKey . '_' . $counter++;
            }
            $existingKeys[] = $key;

            $detectedType = $this->detectType($cleanHeader);

            $columns[] = [
                'header'   => $cleanHeader,
                'key'      => $key,
                'type'     => $detectedType,
                'required' => true,
            ];
        }

        return $columns;
    }

    /**
     * Read headers from CSV file.
     */
    private function parseCsvHeaders(string $filePath): array
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return [];
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return [];
        }

        // Handle possible UTF-8 BOM
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $headers = [];
        $firstRow = fgetcsv($handle, 0, ',');
        if ($firstRow === false || count($firstRow) === 1 && str_contains((string)$firstRow[0], ';')) {
            // Retry with semicolon delimiter
            rewind($handle);
            if ($bom === "\xEF\xBB\xBF") {
                fread($handle, 3);
            }
            $firstRow = fgetcsv($handle, 0, ';');
        }

        if (is_array($firstRow)) {
            $headers = array_filter(array_map('trim', $firstRow), fn($v) => $v !== '');
        }

        fclose($handle);
        return array_values($headers);
    }

    /**
     * Read headers from Excel file (XLSX, XLS) using PhpSpreadsheet.
     */
    private function parseExcelHeaders(string $filePath): array
    {
        try {
            $reader = IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();

            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

            // Check row 1 or row 2 for headers
            $headers = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $val = (string) $worksheet->getCell([$col, 1])->getValue();
                if ($val === '') {
                    // Try row 2 in case row 1 is a title
                    $val = (string) $worksheet->getCell([$col, 2])->getValue();
                }
                if ($val !== '') {
                    $headers[] = $val;
                }
            }

            return $headers;
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Heuristically detect column type from column header name.
     */
    public function detectType(string $header): string
    {
        $h = strtolower(trim($header));

        // NIK
        if (preg_match('/\b(nik|no_ktp|nomor_induk_kependudukan)\b/i', $h) || str_contains($h, 'nik')) {
            return 'nik';
        }

        // NISN
        if (str_contains($h, 'nisn')) {
            return 'nisn';
        }

        // NUPTK
        if (str_contains($h, 'nuptk')) {
            return 'nuptk';
        }

        // NIP
        if (str_contains($h, 'nip')) {
            return 'nip';
        }

        // Jenis Kelamin
        if (str_contains($h, 'jk') || str_contains($h, 'kelamin') || str_contains($h, 'gender') || str_contains($h, 'sex')) {
            return 'gender';
        }

        // Tanggal
        if (str_contains($h, 'tanggal') || str_contains($h, 'tgl') || str_contains($h, 'date') || str_contains($h, 'lahir') || str_contains($h, 'tempo') || str_contains($h, 'tmt')) {
            return 'date';
        }

        // Tempat Lahir
        if (str_contains($h, 'tempat') || str_contains($h, 'tpt_lahir')) {
            return 'birth_place';
        }

        // Email
        if (str_contains($h, 'email') || str_contains($h, 'surel') || str_contains($h, 'mail')) {
            return 'email';
        }

        // No HP / Telepon
        if (str_contains($h, 'hp') || str_contains($h, 'telepon') || str_contains($h, 'telp') || str_contains($h, 'wa') || str_contains($h, 'phone') || str_contains($h, 'ponsel')) {
            return 'phone';
        }

        // Alamat
        if (str_contains($h, 'alamat') || str_contains($h, 'jalan') || str_contains($h, 'dusun') || str_contains($h, 'desa') || str_contains($h, 'kelurahan') || str_contains($h, 'kecamatan')) {
            return 'address';
        }

        // Kota
        if (str_contains($h, 'kota') || str_contains($h, 'kabupaten') || str_contains($h, 'kab')) {
            return 'city';
        }

        // Provinsi
        if (str_contains($h, 'provinsi') || str_contains($h, 'prov')) {
            return 'province';
        }

        // Kode Pos
        if (str_contains($h, 'pos') || str_contains($h, 'kodepos')) {
            return 'postal_code';
        }

        // Uang / Nominal / Tagihan
        if (str_contains($h, 'nominal') || str_contains($h, 'gaji') || str_contains($h, 'tagihan') || str_contains($h, 'biaya') || str_contains($h, 'iuran') || str_contains($h, 'tarif') || str_contains($h, 'spp') || str_contains($h, 'harga') || str_contains($h, 'saldo')) {
            return 'currency';
        }

        // Nama
        if (str_contains($h, 'nama') || str_contains($h, 'name') || str_contains($h, 'siswa') || str_contains($h, 'guru') || str_contains($h, 'pegawai') || str_contains($h, 'wali') || str_contains($h, 'ayah') || str_contains($h, 'ibu')) {
            return 'name';
        }

        // Nomor / Angka
        if (str_contains($h, 'no') || str_contains($h, 'nomor') || str_contains($h, 'jumlah') || str_contains($h, 'qty') || str_contains($h, 'rt') || str_contains($h, 'rw') || str_contains($h, 'urut') || str_contains($h, 'id') || str_contains($h, 'kode')) {
            return 'number';
        }

        // Status
        if (str_contains($h, 'status') || str_contains($h, 'aktif') || str_contains($h, 'flag') || str_contains($h, 'valid')) {
            return 'boolean';
        }

        return 'text';
    }
}

<?php

namespace App\Exports;

use App\Support\DataSanitizer;
use App\Support\TemplateSchema;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

// Class export Excel (XLSX) dengan format template Dapodik
class TestDataExport implements FromArray, WithHeadings, WithTitle, WithEvents
{
    /**
     * @param string[]                          $headers
     * @param string[]                          $keys
     * @param array<int, array<string, mixed>>  $data
     * @param string                            $sheetName
     * @param string                            $category
     */
    public function __construct(
        private readonly array $headers,
        private readonly array $keys,
        private readonly array $data,
        private readonly string $sheetName,
        private readonly string $category,
    ) {}

    public function array(): array
    {
        $rows = [];
        foreach ($this->data as $row) {
            $values = [];
            foreach ($this->keys as $key) {
                $value = $row[$key] ?? '';

                // Handle tagihan numeric and Excel date serial values
                if ($this->category === 'tagihan') {
                    if ($key === 'jumlah_tagihan') {
                        $values[] = is_numeric($value) ? (int) $value : $value;
                        continue;
                    }

                    if ($key === 'tanggal_mulai_tagihan' || $key === 'tanggal_jatuh_tempo_tagihan') {
                        if ($value instanceof \DateTimeInterface) {
                            $values[] = (int) \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($value);
                            continue;
                        }
                        if (is_string($value) && $value !== '') {
                            $dt = \DateTimeImmutable::createFromFormat('d/m/Y', $value)
                                ?: \DateTimeImmutable::createFromFormat('Y-m-d', $value);
                            if ($dt) {
                                $dt = $dt->setTime(0, 0, 0);
                                $values[] = (int) \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($dt);
                                continue;
                            }
                        }
                    }

                    if ($key === 'bulan') {
                        if ($value instanceof \DateTimeInterface) {
                            $values[] = (int) \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($value);
                            continue;
                        }
                        if (is_string($value) && $value !== '') {
                            $dt = \DateTimeImmutable::createFromFormat('m/Y', $value)
                                ?: \DateTimeImmutable::createFromFormat('Y-m', $value);
                            if ($dt) {
                                $dt = $dt->setDate((int) $dt->format('Y'), (int) $dt->format('m'), 1)->setTime(0, 0, 0);
                                $values[] = (int) \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($dt);
                                continue;
                            }
                        }
                    }
                }

                if ($value instanceof \DateTimeInterface) {
                    $value = $this->formatDateForExcel($key, $value);
                } elseif (is_array($value)) {
                    if (isset($value['date']) && is_string($value['date'])) {
                        $value = mb_substr($value['date'], 0, 10);
                    } else {
                        $value = implode(', ', array_filter($value, fn($v) => is_scalar($v)));
                    }
                }

                if (! is_int($value) && ! is_float($value)) {
                    $value = (string) $value;
                }

                $values[] = $value;
            }
            // Sanitize for formula injection
            $rows[] = DataSanitizer::sanitizeRow($values);
        }
        return $rows;
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $delegate = $event->sheet->getDelegate();

                if ($this->category === 'murid') {
                    // Match "format sidigs murid-dapodik (1).xlsx" layout exactly:
                    // Rows 1-3: Notes
                    // Row 4: Blank
                    // Row 5: Main header + Group headers (Data Ayah/Ibu/Wali)
                    // Row 6: Sub-headers for grouped columns (merged vertically for non-grouped)
                    // Row 7+: Student data begins

                    // Insert 5 rows before row 1 (original row 1 headings shifts to row 6, data shifts to row 7+)
                    $delegate->insertNewRowBefore(1, 5);

                    // Set Notes in rows 1-3
                    $delegate->setCellValue('A1', 'Notes:');
                    $delegate->setCellValue('A2', 'Tanda (*) Wajib Diisi');
                    $delegate->setCellValue('A3', 'Kosongkan kolom jika tidak diisi');
                    $delegate->getStyle('A1:A3')->getFont()->setBold(true);

                    $groupHeaders = TemplateSchema::getGroupHeaders($this->category) ?? [];

                    $isGroupedCol = function (int $col1Indexed) use ($groupHeaders): bool {
                        foreach ($groupHeaders as $group) {
                            $startCol = $group['start'] + 1;
                            $endCol = $group['end'] + 1;
                            if ($col1Indexed >= $startCol && $col1Indexed <= $endCol) {
                                return true;
                            }
                        }
                        return false;
                    };

                    // For non-grouped columns, move header from row 6 to row 5, then merge vertically A5:A6
                    $headerCount = count($this->headers);
                    for ($c = 1; $c <= $headerCount; $c++) {
                        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                        if (! $isGroupedCol($c)) {
                            $val = $delegate->getCell("{$colLetter}6")->getValue();
                            $delegate->setCellValue("{$colLetter}5", $val);
                            $delegate->setCellValue("{$colLetter}6", null);
                            $delegate->mergeCells("{$colLetter}5:{$colLetter}6");
                        }
                    }

                    // Set group header labels on row 5 and merge horizontally
                    foreach ($groupHeaders as $group) {
                        $startCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($group['start'] + 1);
                        $endCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($group['end'] + 1);

                        $delegate->setCellValue("{$startCol}5", $group['label']);
                        $delegate->mergeCells("{$startCol}5:{$endCol}5");
                    }

                    // Style headers (rows 5 and 6)
                    $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($headerCount);
                    $headerRange = "A5:{$lastCol}6";
                    $delegate->getStyle($headerRange)->getFont()->setBold(true);
                    $delegate->getStyle($headerRange)->getAlignment()
                        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                } elseif ($this->category === 'pegawai') {
                    // Match "sample_import_pegawai.xlsx" layout:
                    // Rows 1-4: Header metadata
                    // Row 5: Column headers
                    // Row 6+: Employee data begins
                    $delegate->insertNewRowBefore(1, 4);

                    $delegate->setCellValue('A1', 'Daftar Guru');
                    $delegate->setCellValue('A2', 'SMA IT INSAN MANDIRI');
                    $delegate->setCellValue('A3', 'Kecamatan Kec. Jati Sampurna, Kabupaten Kota Bekasi, Provinsi Prov. Jawa Barat');
                    $delegate->setCellValue('A4', 'Tanggal Unduh: ' . date('Y-m-d H:i:s'));
                    $delegate->setCellValue('C4', 'Pengunduh: DataGen QA Studio');
                    $delegate->getStyle('A1')->getFont()->setBold(true);

                    $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($this->headers));
                    $delegate->getStyle("A5:{$lastCol}5")->getFont()->setBold(true);
                } else {
                    // Tagihan & default templates (headers on row 1, data on row 2+)
                    $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($this->headers));
                    $delegate->getStyle("A1:{$lastCol}1")->getFont()->setBold(true);

                    if ($this->category === 'tagihan') {
                        $highestRow = $delegate->getHighestRow();
                        if ($highestRow >= 2) {
                            // Column F: Tanggal Mulai Tagihan (m/d/yyyy)
                            // Column G: Tanggal Jatuh Tempo Tagihan (m/d/yyyy)
                            // Column J: Bulan (mm/yyyy)
                            $delegate->getStyle("F2:F{$highestRow}")->getNumberFormat()->setFormatCode('m/d/yyyy');
                            $delegate->getStyle("G2:G{$highestRow}")->getNumberFormat()->setFormatCode('m/d/yyyy');
                            $delegate->getStyle("J2:J{$highestRow}")->getNumberFormat()->setFormatCode('mm/yyyy');
                        }
                    }
                }

                // Auto-size columns (capped to prevent performance degradation on large sheets)
                $colCount = min(count($this->headers), 72);
                for ($i = 1; $i <= $colCount; $i++) {
                    $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
                    $delegate->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }

    /**
     * Format a DateTime value based on the column's expected format.
     */
    private function formatDateForExcel(string $key, \DateTimeInterface $date): string
    {
        // Murid date fields use dd/mm/yyyy format (e.g. 01/01/2010)
        if (str_contains($key, 'tanggal_lahir') && $this->category === 'murid') {
            return $date->format('d/m/Y');
        }

        // Tagihan specific formats
        if ($this->category === 'tagihan') {
            if ($key === 'bulan') {
                return $date->format('m/Y');
            }
            return $date->format('d/m/Y');
        }

        return $date->format('Y-m-d');
    }

    /**
     * Format date in Indonesian locale: "d mmmm yyyy" (e.g., "2 Mei 2003").
     */
    private function formatIndonesianDate(\DateTimeInterface $date): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $day = (int) $date->format('d');
        $month = $months[(int) $date->format('m')];
        $year = $date->format('Y');

        return "{$day} {$month} {$year}";
    }
}

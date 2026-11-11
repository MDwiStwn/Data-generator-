<?php

namespace Tests\Unit;

use App\Support\DataSanitizer;
use PHPUnit\Framework\TestCase;

class DataSanitizerTest extends TestCase
{
    public function test_sanitizes_formula_injection_prefixes(): void
    {
        $this->assertEquals("'=SUM(A1:A2)", DataSanitizer::sanitizeCell('=SUM(A1:A2)'));
        $this->assertEquals("'+123", DataSanitizer::sanitizeCell('+123'));
        $this->assertEquals("'-123", DataSanitizer::sanitizeCell('-123'));
        $this->assertEquals("'@username", DataSanitizer::sanitizeCell('@username'));
    }

    public function test_leaves_normal_text_unmodified(): void
    {
        $this->assertEquals('Budi Santoso', DataSanitizer::sanitizeCell('Budi Santoso'));
        $this->assertEquals('12345', DataSanitizer::sanitizeCell('12345'));
        $this->assertEquals(100, DataSanitizer::sanitizeCell(100));
        $this->assertNull(DataSanitizer::sanitizeCell(null));
    }

    public function test_sanitizes_array_row(): void
    {
        $row = ['Nama' => 'Budi', 'Formula' => '=CMD()', 'Jumlah' => 500];
        $sanitized = DataSanitizer::sanitizeRow($row);

        $this->assertEquals([
            'Nama' => 'Budi',
            'Formula' => "'=CMD()",
            'Jumlah' => 500,
        ], $sanitized);
    }

    public function test_generates_safe_filename(): void
    {
        $filename = DataSanitizer::safeFilename('pegawai', 'xlsx');
        $this->assertStringStartsWith('pegawai_', $filename);
        $this->assertStringEndsWith('.xlsx', $filename);
        $this->assertDoesNotMatchRegularExpression('/[^a-zA-Z0-9_\-\.]/', $filename);
    }
}

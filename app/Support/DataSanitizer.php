<?php

namespace App\Support;

// Helper pembersih data export biar aman pas dibuka di Excel / Calc
class DataSanitizer
{
    /**
     * Characters that can trigger formula execution in spreadsheet applications.
     */
    private const DANGEROUS_PREFIXES = ['=', '+', '-', '@', "\t", "\r", "\n"];

    /**
     * Sanitize a single cell value for CSV export.
     *
     * If the value starts with a dangerous character, prefix it with a
     * single-quote (') to prevent formula injection when opened in
     * Excel/Google Sheets/LibreOffice Calc.
     *
     * @param mixed $value The cell value to sanitize.
     * @return mixed The sanitized value.
     */
    public static function sanitizeCell(mixed $value): mixed
    {
        if (is_array($value)) {
            if (isset($value['date']) && is_string($value['date'])) {
                $value = mb_substr($value['date'], 0, 10);
            } else {
                $value = implode(', ', array_filter($value, fn($v) => is_scalar($v)));
            }
        }

        if ($value instanceof \DateTimeInterface) {
            $value = $value->format('Y-m-d');
        }

        if (! is_string($value) && ! is_numeric($value) && ! is_null($value)) {
            $value = (string) $value;
        }

        if (! is_string($value) || $value === '') {
            return $value;
        }

        $firstChar = mb_substr($value, 0, 1);

        if (in_array($firstChar, self::DANGEROUS_PREFIXES, true)) {
            return "'" . $value;
        }

        return $value;
    }

    /**
     * Sanitize an entire row for CSV export.
     *
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    public static function sanitizeRow(array $row): array
    {
        return array_map([self::class, 'sanitizeCell'], $row);
    }

    /**
     * Sanitize a full dataset (array of rows) for CSV export.
     *
     * @param array<int, array<string, mixed>> $data
     * @return array<int, array<string, mixed>>
     */
    public static function sanitizeData(array $data): array
    {
        return array_map([self::class, 'sanitizeRow'], $data);
    }

    /**
     * Generate a safe filename for export.
     *
     * Strips potentially dangerous characters, limits length,
     * and ensures the filename is safe for all operating systems.
     */
    public static function safeFilename(string $category, string $extension): string
    {
        $sanitized = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $category);
        $sanitized = mb_substr($sanitized ?? 'export', 0, 50);
        $timestamp = now()->format('Ymd_His');

        return "{$sanitized}_{$timestamp}.{$extension}";
    }
}

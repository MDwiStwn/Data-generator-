<?php

namespace App\Support;

// Helper buat generate nomor identitas Indonesia (NIK, NUPTK, NIP, NISN, dll)
class IdentifierGenerator
{
    /**
     * Generate a synthetic NIK (Nomor Induk Kependudukan).
     * Format: 16 digits — PPRRKKDDMMYYXXXX
     *   PP = province code (2 digits)
     *   RR = regency/city code (2 digits)
     *   KK = district code (2 digits)
     *   DDMMYY = birth date (DD+40 for female)
     *   XXXX = sequential/random 4 digits
     */
    public static function nik(?string $gender = null, ?\DateTimeInterface $birthDate = null): string
    {
        $provinceCodes = ['31', '32', '33', '34', '35', '36', '51', '61', '71', '73', '74', '75', '76'];
        $province = $provinceCodes[array_rand($provinceCodes)];
        $regency = str_pad((string) rand(1, 29), 2, '0', STR_PAD_LEFT);
        $district = str_pad((string) rand(1, 20), 2, '0', STR_PAD_LEFT);

        $birthDate ??= new \DateTimeImmutable(sprintf(
            '%d-%s-%s',
            rand(1970, 2010),
            str_pad((string) rand(1, 12), 2, '0', STR_PAD_LEFT),
            str_pad((string) rand(1, 28), 2, '0', STR_PAD_LEFT)
        ));

        $day = (int) $birthDate->format('d');
        if ($gender === 'P' || $gender === 'Perempuan') {
            $day += 40;
        }

        $ddmmyy = str_pad((string) $day, 2, '0', STR_PAD_LEFT)
            . $birthDate->format('m')
            . $birthDate->format('y');

        $seq = str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT);

        return $province . $regency . $district . $ddmmyy . $seq;
    }

    /**
     * Generate a synthetic No KK (Nomor Kartu Keluarga).
     * Format: 16 digits — PPRRKKDDMMYYXXXX (similar to NIK but sequential part differs)
     */
    public static function noKk(): string
    {
        $provinceCodes = ['31', '32', '33', '34', '35', '36', '51', '61', '71'];
        $province = $provinceCodes[array_rand($provinceCodes)];
        $regency = str_pad((string) rand(1, 29), 2, '0', STR_PAD_LEFT);
        $district = str_pad((string) rand(1, 20), 2, '0', STR_PAD_LEFT);

        $date = sprintf(
            '%s%s%s',
            str_pad((string) rand(1, 28), 2, '0', STR_PAD_LEFT),
            str_pad((string) rand(1, 12), 2, '0', STR_PAD_LEFT),
            str_pad((string) rand(10, 25), 2, '0', STR_PAD_LEFT)
        );

        $seq = str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT);

        return $province . $regency . $district . $date . $seq;
    }

    /**
     * Generate a synthetic NUPTK.
     * Format: approximately 16 digits (the template shows 8-digit samples like 20040517,
     * but real NUPTK is 16 digits). We follow the template sample pattern.
     */
    public static function nuptk(?\DateTimeInterface $birthDate = null): string
    {
        // Template shows 8-digit pattern based on dates (e.g., 20040517)
        // Real NUPTK format: KKKKKKKKMMYYYYXX (16 digits)
        // Using template-observed pattern: appears date-like but synthetic
        $year = rand(1995, 2020);
        $month = str_pad((string) rand(1, 12), 2, '0', STR_PAD_LEFT);
        $day = str_pad((string) rand(1, 28), 2, '0', STR_PAD_LEFT);
        $seq = str_pad((string) rand(1, 99), 2, '0', STR_PAD_LEFT);

        // Match template observed 8-digit pattern
        return $year . $month . $day;
    }

    /**
     * Generate a synthetic NIP (Nomor Induk Pegawai).
     * Template shows 8-digit pattern (17052004, 04092003).
     * We follow the template's 8-digit observed format and ensure high uniqueness.
     */
    public static function nip(?\DateTimeInterface $birthDate = null, ?int $index = null): string
    {
        if ($birthDate) {
            $day = $birthDate->format('d');
            $month = $birthDate->format('m');
            // Unique synthetic year based on birth year, index, and offset
            $baseYear = (int) $birthDate->format('Y');
            $offset = ($index !== null) ? (($index * 7 + 13) % 24) : rand(1, 24);
            $year = 2000 + $offset;
            return $day . $month . $year;
        }

        $day = str_pad((string) rand(1, 28), 2, '0', STR_PAD_LEFT);
        $month = str_pad((string) rand(1, 12), 2, '0', STR_PAD_LEFT);
        $year = rand(2000, 2024);

        return $day . $month . $year;
    }

    /**
     * Generate a synthetic NISN (Nomor Induk Siswa Nasional).
     * Format: 10 digits.
     */
    public static function nisn(): string
    {
        return str_pad((string) rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a synthetic NIS (Nomor Induk Siswa).
     * Format: 4-8 digits (school-specific).
     */
    public static function nis(): string
    {
        return str_pad((string) rand(1000, 99999999), rand(4, 8), '0', STR_PAD_LEFT);
    }

    /**
     * Generate a synthetic NPWP.
     * Format: 15 digits (XX.XXX.XXX.X-XXX.XXX) — stored as numeric string.
     */
    public static function npwp(): string
    {
        $digits = '';
        for ($i = 0; $i < 15; $i++) {
            $digits .= rand(0, 9);
        }
        return $digits;
    }

    /**
     * Generate a synthetic bank account number.
     * Format: 10-15 digits.
     */
    public static function bankAccount(): string
    {
        $length = rand(10, 15);
        $number = '';
        for ($i = 0; $i < $length; $i++) {
            $number .= rand(0, 9);
        }
        return $number;
    }

    /**
     * Generate a synthetic Virtual Account number.
     * Format: typically 16-20 digits, prefixed with bank-specific codes.
     */
    public static function virtualAccount(): string
    {
        $prefixes = ['8800', '7800', '7000', '0088', '1400'];
        $prefix = $prefixes[array_rand($prefixes)];
        $remaining = 16 - strlen($prefix);
        $number = $prefix;
        for ($i = 0; $i < $remaining; $i++) {
            $number .= rand(0, 9);
        }
        return $number;
    }

    /**
     * Generate a synthetic KPS number.
     * Format: 6 digits.
     */
    public static function kpsNumber(): string
    {
        return str_pad((string) rand(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a synthetic KIP number.
     * Format: 10 digits.
     */
    public static function kipNumber(): string
    {
        return str_pad((string) rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a synthetic KKS number.
     * Format: 16 digits.
     */
    public static function kksNumber(): string
    {
        $number = '';
        for ($i = 0; $i < 16; $i++) {
            $number .= rand(0, 9);
        }
        return $number;
    }

    /**
     * Generate a synthetic Akta Lahir registration number.
     * Format: alphanumeric, e.g., "AL-XXXX-XXXXXXXX"
     */
    public static function aktaLahir(): string
    {
        $number = str_pad((string) rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        $seq = str_pad((string) rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        return "AL-{$number}-{$seq}";
    }

    /**
     * Generate a synthetic Indonesian phone number.
     * Format: 08XX-XXXX-XXXX (10-13 digits starting with 08).
     */
    public static function phoneNumber(): string
    {
        $prefixes = ['0812', '0813', '0821', '0822', '0852', '0853', '0856', '0857', '0858', '0878', '0877', '0838', '0831', '0832', '0833'];
        $prefix = $prefixes[array_rand($prefixes)];
        $remainingLength = rand(7, 8);
        $remaining = '';
        for ($i = 0; $i < $remainingLength; $i++) {
            $remaining .= rand(0, 9);
        }
        return $prefix . $remaining;
    }

    /**
     * Generate a synthetic SKHUN number.
     */
    public static function skhun(): string
    {
        $year = rand(2015, 2024);
        $seq = str_pad((string) rand(1, 999999), 6, '0', STR_PAD_LEFT);
        return "DN-06-Dd-{$seq}";
    }

    /**
     * Generate a synthetic SK CPNS number.
     */
    public static function skCpns(): string
    {
        $year = rand(2005, 2023);
        $number = str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT);
        return "SK/{$number}/{$year}";
    }

    /**
     * Generate a synthetic SK Pengangkatan number.
     */
    public static function skPengangkatan(): string
    {
        $year = rand(2005, 2023);
        $number = str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT);
        return "{$number}/KEP/{$year}";
    }

    /**
     * Generate a synthetic Karpeg number.
     */
    public static function karpeg(): string
    {
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $char = $letters[rand(0, 25)];
        $number = str_pad((string) rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        return $char . ' ' . $number;
    }

    /**
     * Generate a synthetic Nomor Peserta Ujian number (13 digits).
     */
    public static function nomorPesertaUjian(): string
    {
        $number = '';
        for ($i = 0; $i < 13; $i++) {
            $number .= rand(0, 9);
        }
        return $number;
    }

    /**
     * Generate a synthetic Nomor Seri Ijazah.
     */
    public static function nomorSeriIjazah(): string
    {
        $prefix = ['DN', 'DD'];
        $p = $prefix[array_rand($prefix)];
        $number = str_pad((string) rand(1000000, 9999999), 7, '0', STR_PAD_LEFT);
        return "{$p}-{$number}";
    }
}

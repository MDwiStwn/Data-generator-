<?php

namespace App\Services\Generators;

use App\Enums\GenerateMode;
use App\Support\IndonesianDataset;

// Generator data dummy Tagihan SPP & Keuangan Siswa
class TagihanGenerator extends AbstractGenerator
{
    private string $tahunAjaran;
    private string $periodePembukuan;

    public function __construct(
        \Faker\Generator $faker,
        string $tahunAjaran = '',
        ?string $periodePembukuan = null
    ) {
        parent::__construct($faker);
        $this->tahunAjaran = $tahunAjaran;
        $this->periodePembukuan = $periodePembukuan ?? $tahunAjaran;
    }

    protected function category(): string
    {
        return 'tagihan';
    }

    // Fungsi generate tiap baris data tagihan
    protected function generateRow(int $index, GenerateMode $mode): array
    {
        // Tipe tagihan sekolah
        $tipePOS = $this->randomEnum(['SPP', 'DPP', 'Lainnya']);

        // List nama POS tagihan kalau tipenya Lainnya (hardcode list)
        $posNamesLainnya = ['PRAKTEK', 'EKSTRAKULIKULER', 'WISATA', 'INFAQ', 'LABORATORIUM', 'UJIAN', 'SERAGAM', 'BUKU'];

        // Prefix kode tagihan
        $kodePrefix = $tipePOS === 'Lainnya'
            ? $this->faker->randomElement($posNamesLainnya)
            : $tipePOS;
        $kodeTagihan = $kodePrefix . '-' . $index;

        // Opsi nama tagihan berdasarkan tipe POS (hardcode)
        $namaTagihanMap = [
            'SPP' => ['Iuran SPP', 'SPP Bulanan', 'Tagihan SPP'],
            'DPP' => ['Uang Gedung', 'Dana Pengembangan Pendidikan', 'Uang Pangkal Masuk'],
            'Lainnya' => ['Uang Ujian', 'Biaya Praktek', 'Seragam Olahraga', 'Tagihan Rapor', 'Tagihan Foto', 'Tagihan Wisata'],
        ];

        $namaOptions = $namaTagihanMap[$tipePOS] ?? ['Tagihan Lainnya'];
        $namaTagihan = $this->faker->randomElement($namaOptions);

        // Range nominal tagihan
        $jumlahTagihan = match ($tipePOS) {
            'SPP' => $this->faker->numberBetween(100000, 500000),
            'DPP' => $this->faker->numberBetween(5000000, 25000000),
            'Lainnya' => $this->faker->numberBetween(10000, 2000000),
        };

        // Ambil tahun ajaran
        $yearParts = explode('/', str_replace(' ', '', $this->tahunAjaran));
        $startYear = isset($yearParts[0]) && is_numeric($yearParts[0]) && (int) $yearParts[0] > 0
            ? (int) $yearParts[0]
            : (int) date('Y');

        $randomMonth = $this->faker->numberBetween(7, 12);
        $startDate = new \DateTimeImmutable("{$startYear}-{$randomMonth}-15");
        $dueDate = $startDate->modify('+' . $this->faker->numberBetween(14, 150) . ' days');
        $bulanDate = new \DateTimeImmutable("{$startYear}-{$randomMonth}-01");

        $tahunAjaran = $this->tahunAjaran;
        $periodePembukuan = $this->periodePembukuan;

        // Nama POS hanya kalau "Lainnya"
        $namaPOS = $tipePOS === 'Lainnya' ? $kodePrefix : '';

        return [
            'kode_tagihan' => $kodeTagihan,
            'nama_tagihan' => $namaTagihan,
            'jumlah_tagihan' => $jumlahTagihan,
            'tahun_ajaran' => $tahunAjaran,
            'periode_pembukuan' => $periodePembukuan,
            'tanggal_mulai_tagihan' => $startDate->format('d/m/Y'),
            'tanggal_jatuh_tempo_tagihan' => $dueDate->format('d/m/Y'),
            'tipe_pos_tagihan' => $tipePOS,
            'nama_pos' => $namaPOS,
            'bulan' => $bulanDate->format('m/Y'),
            'tipe_angsuran' => $this->randomEnum(['Bisa Diangsur', 'Tidak Bisa Diangsur']),
            'catatan' => $this->maybe(30, fn() => $this->faker->sentence(5)),
        ];
    }
}

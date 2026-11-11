<?php

namespace App\Services\Generators;

use App\Enums\GenerateMode;
use App\Support\IdentifierGenerator;
use App\Support\IndonesianDataset;

// Generator data dummy Pegawai / Guru Dapodik
class PegawaiGenerator extends AbstractGenerator
{
    // Simpan NIP yang sudah dipakai biar tidak dobel
    private array $usedNips = ['17052004', '04092003'];

    protected function category(): string
    {
        return 'pegawai';
    }

    // Fungsi buat bikin NIP unik
    private function generateUniqueNip(\DateTimeInterface $birthDate, int $index): string
    {
        do {
            $nip = IdentifierGenerator::nip($birthDate, $index);
        } while (in_array($nip, $this->usedNips, true));

        $this->usedNips[] = $nip;

        return $nip;
    }

    // Generate per satu baris data pegawai
    protected function generateRow(int $index, GenerateMode $mode): array
    {
        // Acak jenis kelamin
        $gender = $this->faker->randomElement(['L', 'P']);
        $nama = $gender === 'L'
            ? $this->faker->firstNameMale() . ' ' . $this->faker->lastName()
            : $this->faker->firstNameFemale() . ' ' . $this->faker->lastName();

        $birthDate = $this->faker->dateTimeBetween('-65 years', '-25 years');
        $statusKepegawaian = $this->randomEnum(['PNS', 'PPPK', 'GTY/PTY', 'Guru Honor Sekolah', 'Tenaga Honor Sekolah']);
        $isPns = in_array($statusKepegawaian, ['PNS', 'PPPK'], true);
        $statusPerkawinan = $this->randomEnum(['Kawin', 'Belum Menikah', 'Cerai Hidup', 'Cerai Mati']);
        $isMarried = $statusPerkawinan === 'Kawin';

        // Bank hardcode / random list
        $bank = $this->maybe(60, fn() => $this->randomEnum(IndonesianDataset::banks()));

        return [
            'no' => $index,
            'nama' => $nama,
            'nuptk' => $this->maybe(70, fn() => IdentifierGenerator::nuptk($birthDate)),
            'jk' => $gender,
            'tempat_lahir' => strtoupper($this->faker->city()),
            'tanggal_lahir' => $birthDate->format('Y-m-d'),
            'nip' => $this->generateUniqueNip($birthDate, $index),
            'status_kepegawaian' => $statusKepegawaian,
            'jenis_ptk' => $this->randomEnum(['Guru Kelas', 'Guru Mapel', 'Guru BK', 'Guru Pendamping', 'Guru Pendamping Khusus', 'Guru TIK', 'Tenaga Administrasi Sekolah', 'Kepala Sekolah']),
            'agama' => $this->randomEnum(IndonesianDataset::religions()),
            'alamat_jalan' => $this->faker->streetAddress(),
            'rt' => $this->faker->numberBetween(1, 20),
            'rw' => $this->faker->numberBetween(1, 15),
            'nama_dusun' => $this->faker->randomElement(IndonesianDataset::dusun()),
            'desa_kelurahan' => $this->faker->randomElement(IndonesianDataset::kelurahan()),
            'kecamatan' => $this->faker->randomElement(IndonesianDataset::kecamatan()),
            'kode_pos' => $this->faker->numberBetween(10000, 99999),
            'telepon' => $this->maybe(30, fn() => IdentifierGenerator::phoneNumber()),
            'hp' => IdentifierGenerator::phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'tugas_tambahan' => $this->maybe(20, fn() => $this->randomEnum(['Kepala Sekolah', 'Wakil Kepala Sekolah', 'Wali Kelas', 'Pembina OSIS'])),
            'sk_cpns' => $isPns ? IdentifierGenerator::skCpns() : '',
            'tanggal_cpns' => $isPns ? $this->faker->dateTimeBetween('-30 years', '-5 years')->format('Y-m-d') : '',
            'sk_pengangkatan' => $this->maybe(50, fn() => IdentifierGenerator::skPengangkatan()),
            'tmt_pengangkatan' => $this->maybe(50, fn() => $this->faker->dateTimeBetween('-25 years', '-1 year')->format('Y-m-d')),
            'lembaga_pengangkatan' => $this->maybe(50, fn() => $this->randomEnum(['Pemerintah Pusat', 'Pemerintah Provinsi', 'Pemerintah Kab/Kota', 'Yayasan'])),
            'pangkat_golongan' => $isPns ? $this->randomEnum(['II/a', 'II/b', 'III/a', 'III/b', 'III/c', 'III/d', 'IV/a', 'IV/b']) : '',
            'sumber_gaji' => $this->randomEnum(['APBN', 'APBD Provinsi', 'APBD Kab/Kota', 'Yayasan', 'Sekolah', 'Lainnya']),
            'nama_ibu_kandung' => $this->faker->firstNameFemale() . ' ' . $this->faker->lastName(),
            'status_perkawinan' => $statusPerkawinan,
            'nama_suami_istri' => $isMarried ? $this->faker->name() : '',
            'nip_suami_istri' => ($isMarried && $this->faker->boolean(20)) ? IdentifierGenerator::nip() : '',
            'pekerjaan_suami_istri' => $isMarried ? $this->faker->randomElement(IndonesianDataset::jobs()) : '',
            'tmt_pns' => $isPns ? $this->faker->dateTimeBetween('-25 years', '-1 year')->format('Y-m-d') : '',
            'sudah_lisensi_kepala_sekolah' => $this->maybe(10, fn() => $this->randomEnum(['Ya', 'Tidak'])),
            'pernah_diklat_kepengawasan' => $this->maybe(10, fn() => $this->randomEnum(['Ya', 'Tidak'])),
            'keahlian_braille' => $this->maybe(5, fn() => $this->randomEnum(['Ya', 'Tidak'])),
            'keahlian_bahasa_isyarat' => $this->maybe(5, fn() => $this->randomEnum(['Ya', 'Tidak'])),
            'npwp' => $this->maybe(60, fn() => IdentifierGenerator::npwp()),
            'nama_wajib_pajak' => $this->maybe(60, fn() => $nama),
            'kewarganegaraan' => 'ID',
            'bank' => $bank,
            'nomor_rekening_bank' => $bank !== '' ? IdentifierGenerator::bankAccount() : '',
            'rekening_atas_nama' => $bank !== '' ? $nama : '',
            'nik' => $this->maybe(80, fn() => IdentifierGenerator::nik($gender === 'L' ? null : 'P', $birthDate)),
            'no_kk' => $this->maybe(70, fn() => IdentifierGenerator::noKk()),
            'karpeg' => $isPns ? $this->maybe(50, fn() => IdentifierGenerator::karpeg()) : '',
            'karis_karsu' => ($isPns && $isMarried) ? $this->maybe(40, fn() => IdentifierGenerator::karpeg()) : '',
            'lintang' => $this->maybe(30, fn() => (string) $this->faker->latitude(-8.5, -6.0)),
            'bujur' => $this->maybe(30, fn() => (string) $this->faker->longitude(106.0, 114.0)),
            'nuks' => $this->maybe(10, fn() => strtoupper($this->faker->bothify('??######'))),
        ];
    }
}

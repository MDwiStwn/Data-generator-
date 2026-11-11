<?php

namespace App\Services\Generators;

use App\Enums\GenerateMode;
use App\Support\IdentifierGenerator;
use App\Support\IndonesianDataset;

// Generator data dummy Siswa / Peserta Didik format Dapodik
class MuridGenerator extends AbstractGenerator
{
    /** @var array<string> */
    private array $usedStudentUsernames = [];

    /** @var array<string> */
    private array $usedWaliUsernames = [];

    /** @var array<string> */
    private array $usedNis = [];

    /** @var array<string> */
    private array $usedNisn = [];

    protected function category(): string
    {
        return 'murid';
    }

    protected function generateRow(int $index, GenerateMode $mode): array
    {
        $gender = $this->faker->randomElement(['Laki - Laki', 'Perempuan']);
        $isMale = $gender === 'Laki - Laki';

        $nama = $isMale
            ? $this->faker->firstNameMale() . ' ' . $this->faker->lastName()
            : $this->faker->firstNameFemale() . ' ' . $this->faker->lastName();

        $namaPanggilan = explode(' ', $nama)[0];

        // Guaranteed unique student username
        $baseStudentUser = strtolower((string) preg_replace('/[^a-z0-9]/', '', $nama));
        $studentUser = $baseStudentUser;
        $counter = 1;
        while (in_array($studentUser, $this->usedStudentUsernames, true)) {
            $studentUser = $baseStudentUser . $counter;
            $counter++;
        }
        $this->usedStudentUsernames[] = $studentUser;

        // Guaranteed unique wali username
        $cleanNickname = strtolower((string) preg_replace('/[^a-z0-9]/', '', $namaPanggilan));
        $baseWaliUser = 'ortu' . $cleanNickname;
        $counter = 1;
        while (in_array($baseWaliUser, $this->usedWaliUsernames, true)) {
            $baseWaliUser = 'ortu' . $cleanNickname . $counter;
            $counter++;
        }
        $this->usedWaliUsernames[] = $baseWaliUser;
        $waliUsername = $baseWaliUser;

        $birthDate = $this->faker->dateTimeBetween('-18 years', '-6 years');

        $hasWali = $this->faker->boolean(30);
        $penerimaKps = $this->randomEnum(['Ya', 'Tidak']);
        $penerimaKip = $this->randomEnum(['Ya', 'Tidak']);
        $layakPip = $this->randomEnum(['Ya', 'Tidak']);
        $bank = $this->maybe(30, fn() => $this->faker->randomElement(IndonesianDataset::banks()));

        $jenjangOptions = ['Tidak Memiliki Jenjang', 'SD Sederajat', 'SMP Sederajat', 'SMA Sederajat', 'S1 / D4', 'S2', 'S3', 'D1', 'D2', 'D3', 'Lainnya'];
        $incomeTiers = [
            'Kurang Dari Rp.500.000',
            'Rp.500.000 - Rp.999.999',
            'Rp.1.000.000 - Rp.1.999.999',
            'Rp.2.000.000 - Rp.3.499.999',
            'Rp.3.500.000 - Rp.4.999.999',
            'Rp.5.000.000 - Rp.9.999.999',
            'Rp.10.000.000 - Rp.20.000.000',
            'Lebih Dari Rp.20.000.000',
        ];

        // Job and income consistency for parents
        $jobs = ['Karyawan Swasta', 'Wiraswasta', 'PNS', 'Pedagang', 'Petani', 'Buruh', 'Pensiunan', 'Guru'];
        
        // Ayah
        $ayahJob = $this->faker->randomElement($jobs);
        $ayahPenghasilan = $this->faker->randomElement($incomeTiers);

        // Ibu
        $ibuJobOptions = array_merge($jobs, ['Ibu Rumah Tangga', 'Tidak Bekerja']);
        $ibuJob = $this->faker->randomElement($ibuJobOptions);
        if ($ibuJob === 'Tidak Bekerja' || $ibuJob === 'Ibu Rumah Tangga') {
            $ibuPenghasilan = 'Tidak Berpenghasilan';
        } else {
            $ibuPenghasilan = $this->faker->randomElement($incomeTiers);
        }

        // Wali
        $waliJob = $hasWali ? $this->faker->randomElement($jobs) : '';
        $waliPenghasilan = $hasWali ? $this->faker->randomElement($incomeTiers) : '';

        $ayahBirthDate = $this->faker->dateTimeBetween('-60 years', '-30 years');
        $ibuBirthDate = $this->faker->dateTimeBetween('-55 years', '-28 years');
        $waliBirthDate = $hasWali ? $this->faker->dateTimeBetween('-60 years', '-25 years') : null;

        $birthDateStr = $birthDate->format('d/m/Y');
        $ayahBirthDateStr = $ayahBirthDate->format('d/m/Y');
        $ibuBirthDateStr = $ibuBirthDate->format('d/m/Y');
        $waliBirthDateStr = $hasWali && $waliBirthDate ? $waliBirthDate->format('d/m/Y') : '';

        // Guaranteed unique NIS & NISN
        do {
            $nis = IdentifierGenerator::nis();
        } while (in_array($nis, $this->usedNis, true));
        $this->usedNis[] = $nis;

        do {
            $nisn = IdentifierGenerator::nisn();
        } while (in_array($nisn, $this->usedNisn, true));
        $this->usedNisn[] = $nisn;

        // Clean dusun names
        $dusunList = array_values(array_filter(IndonesianDataset::dusun(), fn($d) => $d !== '-'));
        $dusun = $this->faker->randomElement($dusunList);

        return [
            'no' => $index,
            'nama' => $nama,
            'nama_panggilan' => $namaPanggilan,
            'username_login_murid' => $studentUser,
            'password_login_murid' => $studentUser . '123',
            'nis' => $nis,
            'jenis_kelamin' => $gender,
            'nisn' => $this->maybe(70, fn() => $nisn),
            'tempat_lahir' => $this->faker->randomElement(IndonesianDataset::cities()),
            'tanggal_lahir' => $birthDateStr,
            // Guaranteed valid 16-digit NIK for student
            'nik' => IdentifierGenerator::nik($isMale ? null : 'P', $birthDate),
            'agama' => $this->randomEnum(IndonesianDataset::religions()),
            'alamat' => $this->faker->streetAddress(),
            'rt' => $this->faker->numberBetween(1, 30),
            'rw' => $this->faker->numberBetween(1, 20),
            'dusun' => $dusun,
            'kelurahan' => $this->faker->randomElement(IndonesianDataset::kelurahan()),
            'kecamatan' => $this->faker->randomElement(IndonesianDataset::kecamatan()),
            'kode_pos' => $this->faker->numberBetween(10000, 99999),
            'jenis_tinggal' => $this->randomEnum(['Bersama orang tua', 'Wali', 'Kos', 'Asrama', 'Panti Asuhan', 'Lainnya']),
            'alat_transportasi' => $this->randomEnum(['Jalan Kaki', 'Sepeda', 'Sepeda Motor', 'Ojek', 'Mobil Pribadi', 'Lainnnya']),
            'telepon' => $this->maybe(20, fn() => IdentifierGenerator::phoneNumber()),
            'hp' => IdentifierGenerator::phoneNumber(),
            'email' => $this->maybe(50, fn() => $this->faker->unique()->safeEmail()),
            'skhun' => $this->maybe(20, fn() => IdentifierGenerator::skhun()),
            'penerima_kps' => $penerimaKps,
            'no_kps' => $penerimaKps === 'Ya' ? IdentifierGenerator::kpsNumber() : '',
            'username_login_walimurid' => $waliUsername,
            'password_login_walimurid' => $waliUsername . '123',

            // Data Ayah - Always complete NIK and date
            'ayah_nama' => $this->faker->firstNameMale() . ' ' . $this->faker->lastName(),
            'ayah_tanggal_lahir' => $ayahBirthDateStr,
            'ayah_jenjang_pendidikan' => $this->randomEnum($jenjangOptions),
            'ayah_pekerjaan' => $ayahJob,
            'ayah_penghasilan' => $ayahPenghasilan,
            'ayah_nik' => IdentifierGenerator::nik(null, $ayahBirthDate),

            // Data Ibu - Always complete NIK and date (MANDATORY for Dapodik / school validation)
            'ibu_nama' => $this->faker->firstNameFemale() . ' ' . $this->faker->lastName(),
            'ibu_tanggal_lahir' => $ibuBirthDateStr,
            'ibu_jenjang_pendidikan' => $this->randomEnum($jenjangOptions),
            'ibu_pekerjaan' => $ibuJob,
            'ibu_penghasilan' => $ibuPenghasilan,
            'ibu_nik' => IdentifierGenerator::nik('P', $ibuBirthDate),

            // Data Wali (optional, ~30%) - Complete when present
            'wali_nama' => $hasWali ? $this->faker->name() : '',
            'wali_tanggal_lahir' => $waliBirthDateStr,
            'wali_jenjang_pendidikan' => $hasWali ? $this->randomEnum($jenjangOptions) : '',
            'wali_pekerjaan' => $waliJob,
            'wali_penghasilan' => $waliPenghasilan,
            'wali_nik' => $hasWali ? IdentifierGenerator::nik(null, $waliBirthDate) : '',

            // Additional data
            // Khusus data murid, kelas (Rombel Saat Ini) dibiarkan kosong agar diisi mandiri oleh QA
            'rombel_saat_ini' => '',
            'nomor_peserta_ujian_nasional' => $this->maybe(20, fn() => IdentifierGenerator::nomorPesertaUjian()),
            'nomor_seri_ijazah' => $this->maybe(15, fn() => IdentifierGenerator::nomorSeriIjazah()),
            'penerima_kip' => $penerimaKip,
            'nomor_kip' => $penerimaKip === 'Ya' ? IdentifierGenerator::kipNumber() : '',
            'nama_di_kip' => $penerimaKip === 'Ya' ? $nama : '',
            'nomor_kks' => $this->maybe(20, fn() => IdentifierGenerator::kksNumber()),
            'no_registrasi_akta_lahir' => $this->maybe(40, fn() => IdentifierGenerator::aktaLahir()),
            'bank' => $bank,
            'nomor_rekening_bank' => $bank !== '' ? IdentifierGenerator::bankAccount() : '',
            'rekening_atas_nama' => $bank !== '' ? $nama : '',
            'layak_pip' => $layakPip,
            'alasan_layak_pip' => $layakPip === 'Ya' ? $this->faker->randomElement(['Yatim', 'Piatu', 'Yatim Piatu', 'Keluarga Miskin', 'Korban Bencana']) : '',
            'kebutuhan_khusus' => $this->maybe(5, fn() => $this->faker->randomElement(['Tunanetra', 'Tunarungu', 'Tunagrahita', 'Tunadaksa', 'Autis'])),
            'sekolah_asal' => $this->maybe(60, fn() => $this->faker->randomElement(IndonesianDataset::schoolNames())),
            'anak_ke_berapa' => $this->faker->numberBetween(1, 8),
            'lintang' => $this->maybe(20, fn() => (string) $this->faker->latitude(-8.5, -6.0)),
            'bujur' => $this->maybe(20, fn() => (string) $this->faker->longitude(106.0, 114.0)),
            'no_kk' => IdentifierGenerator::noKk(),
            'berat_badan' => $this->faker->numberBetween(30, 90),
            'tinggi_badan' => $this->faker->numberBetween(120, 185),
            'lingkar_kepala' => $this->faker->numberBetween(48, 58),
            'jml_saudara_kandung' => $this->faker->numberBetween(0, 6),
            'jarak_rumah_ke_sekolah' => round($this->faker->randomFloat(1, 0.5, 20.0), 1),
            'nomor_virtual_account' => $this->maybe(30, fn() => IdentifierGenerator::virtualAccount()),
        ];
    }
}

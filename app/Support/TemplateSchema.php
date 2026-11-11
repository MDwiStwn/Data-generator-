<?php

namespace App\Support;

// Daftar susunan kolom template bawaan Dapodik (Pegawai, Murid, Tagihan)
class TemplateSchema
{
    /**
     * Column definition structure:
     * [
     *   'key'      => string,   // internal identifier (snake_case of header)
     *   'header'   => string,   // exact header text from the template
     *   'required' => bool,
     *   'type'     => string,   // string|integer|date|datetime|decimal|numeric_string
     *   'format'   => ?string,  // e.g. 'Y-m-d', 'mm-dd-yy'
     *   'enum'     => ?array,   // allowed values
     *   'group'    => ?string,  // group label for sub-headers (Data Ayah, Data Ibu, Data Wali)
     * ]
     */

    // ─── Pegawai / Guru ─────────────────────────────────────────────

    public static function pegawaiColumns(): array
    {
        return [
            ['key' => 'no', 'header' => 'No', 'required' => true, 'type' => 'integer', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'nama', 'header' => 'Nama', 'required' => true, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'nuptk', 'header' => 'NUPTK', 'required' => false, 'type' => 'numeric_string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'jk', 'header' => 'JK', 'required' => true, 'type' => 'string', 'format' => null, 'enum' => ['L', 'P'], 'group' => null],
            ['key' => 'tempat_lahir', 'header' => 'Tempat Lahir', 'required' => true, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'tanggal_lahir', 'header' => 'Tanggal Lahir', 'required' => true, 'type' => 'string', 'format' => 'Y-m-d', 'enum' => null, 'group' => null],
            ['key' => 'nip', 'header' => 'NIP', 'required' => false, 'type' => 'numeric_string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'status_kepegawaian', 'header' => 'Status Kepegawaian', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => ['PNS', 'PPPK', 'GTY/PTY', 'Guru Honor Sekolah', 'Tenaga Honor Sekolah'], 'group' => null],
            ['key' => 'jenis_ptk', 'header' => 'Jenis PTK', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => ['Guru Kelas', 'Guru Mapel', 'Guru BK', 'Guru Pendamping', 'Guru Pendamping Khusus', 'Guru TIK', 'Tenaga Administrasi Sekolah', 'Kepala Sekolah'], 'group' => null],
            ['key' => 'agama', 'header' => 'Agama', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'], 'group' => null],
            ['key' => 'alamat_jalan', 'header' => 'Alamat Jalan', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'rt', 'header' => 'RT', 'required' => false, 'type' => 'integer', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'rw', 'header' => 'RW', 'required' => false, 'type' => 'integer', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'nama_dusun', 'header' => 'Nama Dusun', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'desa_kelurahan', 'header' => 'Desa/Kelurahan', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'kecamatan', 'header' => 'Kecamatan', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'kode_pos', 'header' => 'Kode Pos', 'required' => false, 'type' => 'integer', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'telepon', 'header' => 'Telepon', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'hp', 'header' => 'HP', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'email', 'header' => 'Email', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'tugas_tambahan', 'header' => 'Tugas Tambahan', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => ['Kepala Sekolah', 'Wakil Kepala Sekolah', 'Wali Kelas', 'Pembina OSIS', ''], 'group' => null],
            ['key' => 'sk_cpns', 'header' => 'SK CPNS', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'tanggal_cpns', 'header' => 'Tanggal CPNS', 'required' => false, 'type' => 'string', 'format' => 'Y-m-d', 'enum' => null, 'group' => null],
            ['key' => 'sk_pengangkatan', 'header' => 'SK Pengangkatan', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'tmt_pengangkatan', 'header' => 'TMT Pengangkatan', 'required' => false, 'type' => 'string', 'format' => 'Y-m-d', 'enum' => null, 'group' => null],
            ['key' => 'lembaga_pengangkatan', 'header' => 'Lembaga Pengangkatan', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => ['Pemerintah Pusat', 'Pemerintah Provinsi', 'Pemerintah Kab/Kota', 'Yayasan', ''], 'group' => null],
            ['key' => 'pangkat_golongan', 'header' => 'Pangkat Golongan', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => ['I/a', 'I/b', 'I/c', 'I/d', 'II/a', 'II/b', 'II/c', 'II/d', 'III/a', 'III/b', 'III/c', 'III/d', 'IV/a', 'IV/b', 'IV/c', 'IV/d', 'IV/e', ''], 'group' => null],
            ['key' => 'sumber_gaji', 'header' => 'Sumber Gaji', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => ['APBN', 'APBD Provinsi', 'APBD Kab/Kota', 'Yayasan', 'Sekolah', 'Lainnya', ''], 'group' => null],
            ['key' => 'nama_ibu_kandung', 'header' => 'Nama Ibu Kandung', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'status_perkawinan', 'header' => 'Status Perkawinan', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => ['Kawin', 'Belum Menikah', 'Cerai Hidup', 'Cerai Mati'], 'group' => null],
            ['key' => 'nama_suami_istri', 'header' => 'Nama Suami/Istri', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'nip_suami_istri', 'header' => 'NIP Suami/Istri', 'required' => false, 'type' => 'numeric_string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'pekerjaan_suami_istri', 'header' => 'Pekerjaan Suami/Istri', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'tmt_pns', 'header' => 'TMT PNS', 'required' => false, 'type' => 'string', 'format' => 'Y-m-d', 'enum' => null, 'group' => null],
            ['key' => 'sudah_lisensi_kepala_sekolah', 'header' => 'Sudah Lisensi Kepala Sekolah', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => ['Ya', 'Tidak', ''], 'group' => null],
            ['key' => 'pernah_diklat_kepengawasan', 'header' => 'Pernah Diklat Kepengawasan', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => ['Ya', 'Tidak', ''], 'group' => null],
            ['key' => 'keahlian_braille', 'header' => 'Keahlian Braille', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => ['Ya', 'Tidak', ''], 'group' => null],
            ['key' => 'keahlian_bahasa_isyarat', 'header' => 'Keahlian Bahasa Isyarat', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => ['Ya', 'Tidak', ''], 'group' => null],
            ['key' => 'npwp', 'header' => 'NPWP', 'required' => false, 'type' => 'numeric_string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'nama_wajib_pajak', 'header' => 'Nama Wajib Pajak', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'kewarganegaraan', 'header' => 'Kewarganegaraan', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => ['ID', 'WNA', ''], 'group' => null],
            ['key' => 'bank', 'header' => 'Bank', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => ['BCA', 'BNI', 'BRI', 'MANDIRI', 'BSI', 'BTN', ''], 'group' => null],
            ['key' => 'nomor_rekening_bank', 'header' => 'Nomor Rekening Bank', 'required' => false, 'type' => 'numeric_string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'rekening_atas_nama', 'header' => 'Rekening Atas Nama', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'nik', 'header' => 'NIK', 'required' => false, 'type' => 'numeric_string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'no_kk', 'header' => 'No KK', 'required' => false, 'type' => 'numeric_string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'karpeg', 'header' => 'Karpeg', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'karis_karsu', 'header' => 'Karis/Karsu', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'lintang', 'header' => 'Lintang', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'bujur', 'header' => 'Bujur', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'nuks', 'header' => 'NUKS', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
        ];
    }

    // ─── Murid / Dapodik ────────────────────────────────────────────

    public static function muridColumns(): array
    {
        return [
            // Student basic data
            ['key' => 'no', 'header' => 'No', 'required' => false, 'type' => 'integer', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'nama', 'header' => 'Nama (*)', 'required' => true, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'nama_panggilan', 'header' => 'Nama Panggilan', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'username_login_murid', 'header' => 'Username Login Murid', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'password_login_murid', 'header' => 'Password Login Murid', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'nis', 'header' => 'NIS', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'jenis_kelamin', 'header' => 'Jenis Kelamin (*)', 'required' => true, 'type' => 'string', 'format' => null, 'enum' => ['Laki - Laki', 'Perempuan'], 'group' => null],
            ['key' => 'nisn', 'header' => 'NISN', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'tempat_lahir', 'header' => 'Tempat Lahir (*)', 'required' => true, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'tanggal_lahir', 'header' => 'Tanggal Lahir (*)', 'required' => true, 'type' => 'datetime', 'format' => 'dd/mm/yyyy', 'enum' => null, 'group' => null],
            ['key' => 'nik', 'header' => 'NIK', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'agama', 'header' => 'Agama', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'], 'group' => null],
            ['key' => 'alamat', 'header' => 'Alamat', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'rt', 'header' => 'RT', 'required' => false, 'type' => 'integer', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'rw', 'header' => 'RW', 'required' => false, 'type' => 'integer', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'dusun', 'header' => 'Dusun', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'kelurahan', 'header' => 'Kelurahan', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'kecamatan', 'header' => 'Kecamatan', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'kode_pos', 'header' => 'Kode Pos', 'required' => false, 'type' => 'integer', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'jenis_tinggal', 'header' => 'Jenis Tinggal', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => ['Bersama orang tua', 'Wali', 'Kos', 'Asrama', 'Panti Asuhan', 'Lainnya'], 'group' => null],
            ['key' => 'alat_transportasi', 'header' => 'Alat Transportasi', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => ['Jalan Kaki', 'Sepeda', 'Sepeda Motor', 'Ojek', 'Mobil Pribadi', 'Lainnnya'], 'group' => null],
            ['key' => 'telepon', 'header' => 'Telepon', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'hp', 'header' => 'HP', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'email', 'header' => 'Email', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'skhun', 'header' => 'SKHUN', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'penerima_kps', 'header' => 'Penerima KPS', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => ['Ya', 'Tidak'], 'group' => null],
            ['key' => 'no_kps', 'header' => 'No. KPS', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'username_login_walimurid', 'header' => 'Username Login Walimurid', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'password_login_walimurid', 'header' => 'Password Login Walimurid', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],

            // Data Ayah (AD-AI)
            ['key' => 'ayah_nama', 'header' => 'Nama', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => 'Data Ayah'],
            ['key' => 'ayah_tanggal_lahir', 'header' => 'Tanggal Lahir (*)', 'required' => true, 'type' => 'datetime', 'format' => 'dd/mm/yyyy', 'enum' => null, 'group' => 'Data Ayah'],
            ['key' => 'ayah_jenjang_pendidikan', 'header' => 'Jenjang Pendidikan', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => ['Tidak Memiliki Jenjang', 'SD Sederajat', 'SMP Sederajat', 'SMA Sederajat', 'S1 / D4', 'S2', 'S3', 'D1', 'D2', 'D3', 'Lainnya'], 'group' => 'Data Ayah'],
            ['key' => 'ayah_pekerjaan', 'header' => 'Pekerjaan', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => 'Data Ayah'],
            ['key' => 'ayah_penghasilan', 'header' => 'Penghasilan', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => ['Tidak Berpenghasilan', 'Kurang Dari Rp.500.000', 'Rp.500.000 - Rp.999.999', 'Rp.1.000.000 - Rp.1.999.999', 'Rp.2.000.000 - Rp.3.499.999', 'Rp.3.500.000 - Rp.4.999.999', 'Rp.5.000.000 - Rp.9.999.999', 'Rp.10.000.000 - Rp.20.000.000', 'Lebih Dari Rp.20.000.000'], 'group' => 'Data Ayah'],
            ['key' => 'ayah_nik', 'header' => 'NIK', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => 'Data Ayah'],

            // Data Ibu (AJ-AO)
            ['key' => 'ibu_nama', 'header' => 'Nama', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => 'Data Ibu'],
            ['key' => 'ibu_tanggal_lahir', 'header' => 'Tanggal Lahir (*)', 'required' => true, 'type' => 'datetime', 'format' => 'dd/mm/yyyy', 'enum' => null, 'group' => 'Data Ibu'],
            ['key' => 'ibu_jenjang_pendidikan', 'header' => 'Jenjang Pendidikan', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => ['Tidak Memiliki Jenjang', 'SD Sederajat', 'SMP Sederajat', 'SMA Sederajat', 'S1 / D4', 'S2', 'S3', 'D1', 'D2', 'D3', 'Lainnya'], 'group' => 'Data Ibu'],
            ['key' => 'ibu_pekerjaan', 'header' => 'Pekerjaan', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => 'Data Ibu'],
            ['key' => 'ibu_penghasilan', 'header' => 'Penghasilan', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => ['Tidak Berpenghasilan', 'Kurang Dari Rp.500.000', 'Rp.500.000 - Rp.999.999', 'Rp.1.000.000 - Rp.1.999.999', 'Rp.2.000.000 - Rp.3.499.999', 'Rp.3.500.000 - Rp.4.999.999', 'Rp.5.000.000 - Rp.9.999.999', 'Rp.10.000.000 - Rp.20.000.000', 'Lebih Dari Rp.20.000.000'], 'group' => 'Data Ibu'],
            ['key' => 'ibu_nik', 'header' => 'NIK', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => 'Data Ibu'],

            // Data Wali (AP-AU)
            ['key' => 'wali_nama', 'header' => 'Nama', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => 'Data Wali'],
            ['key' => 'wali_tanggal_lahir', 'header' => 'Tanggal Lahir (*)', 'required' => false, 'type' => 'datetime', 'format' => 'dd/mm/yyyy', 'enum' => null, 'group' => 'Data Wali'],
            ['key' => 'wali_jenjang_pendidikan', 'header' => 'Jenjang Pendidikan', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => ['Tidak Memiliki Jenjang', 'SD Sederajat', 'SMP Sederajat', 'SMA Sederajat', 'S1 / D4', 'S2', 'S3', 'D1', 'D2', 'D3', 'Lainnya'], 'group' => 'Data Wali'],
            ['key' => 'wali_pekerjaan', 'header' => 'Pekerjaan', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => 'Data Wali'],
            ['key' => 'wali_penghasilan', 'header' => 'Penghasilan', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => ['Tidak Berpenghasilan', 'Kurang Dari Rp.500.000', 'Rp.500.000 - Rp.999.999', 'Rp.1.000.000 - Rp.1.999.999', 'Rp.2.000.000 - Rp.3.499.999', 'Rp.3.500.000 - Rp.4.999.999', 'Rp.5.000.000 - Rp.9.999.999', 'Rp.10.000.000 - Rp.20.000.000', 'Lebih Dari Rp.20.000.000'], 'group' => 'Data Wali'],
            ['key' => 'wali_nik', 'header' => 'NIK', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => 'Data Wali'],

            // Additional student data (AV-BT)
            ['key' => 'rombel_saat_ini', 'header' => 'Rombel Saat Ini', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'nomor_peserta_ujian_nasional', 'header' => 'Nomor Peserta Ujian Nasional', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'nomor_seri_ijazah', 'header' => 'Nomor Seri Ijazah', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'penerima_kip', 'header' => 'Penerima KIP', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => ['Ya', 'Tidak'], 'group' => null],
            ['key' => 'nomor_kip', 'header' => 'Nomor KIP', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'nama_di_kip', 'header' => 'Nama Di KIP', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'nomor_kks', 'header' => 'Nomor KKS', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'no_registrasi_akta_lahir', 'header' => 'No Registrasi Akta Lahir', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'bank', 'header' => 'Bank', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => ['BCA', 'BNI', 'BRI', 'MANDIRI', 'BSI', 'BTN', ''], 'group' => null],
            ['key' => 'nomor_rekening_bank', 'header' => 'Nomor Rekening Bank', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'rekening_atas_nama', 'header' => 'Rekening Atas Nama', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'layak_pip', 'header' => 'Layak PIP (Usulan dari Sekolah)', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => ['Ya', 'Tidak'], 'group' => null],
            ['key' => 'alasan_layak_pip', 'header' => 'Alasan Layak PIP', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'kebutuhan_khusus', 'header' => 'Kebutuhan Khusus', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'sekolah_asal', 'header' => 'Sekolah Asal', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'anak_ke_berapa', 'header' => 'Anak Ke Berapa', 'required' => false, 'type' => 'integer', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'lintang', 'header' => 'Lintang', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'bujur', 'header' => 'Bujur', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'no_kk', 'header' => 'No. KK', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'berat_badan', 'header' => 'Berat Badan', 'required' => false, 'type' => 'integer', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'tinggi_badan', 'header' => 'Tinggi Badan', 'required' => false, 'type' => 'integer', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'lingkar_kepala', 'header' => 'Lingkar Kepala', 'required' => false, 'type' => 'integer', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'jml_saudara_kandung', 'header' => 'Jml. Saudara Kandung', 'required' => false, 'type' => 'integer', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'jarak_rumah_ke_sekolah', 'header' => 'Jarak Rumah ke Sekolah (KM)', 'required' => false, 'type' => 'decimal', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'nomor_virtual_account', 'header' => 'Nomor Virtual Account', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
        ];
    }

    // ─── Tagihan ────────────────────────────────────────────────────

    public static function tagihanColumns(): array
    {
        return [
            ['key' => 'kode_tagihan', 'header' => 'Kode Tagihan', 'required' => true, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'nama_tagihan', 'header' => 'Nama Tagihan', 'required' => true, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'jumlah_tagihan', 'header' => 'Jumlah Tagihan', 'required' => true, 'type' => 'integer', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'tahun_ajaran', 'header' => 'Tahun Ajaran', 'required' => true, 'type' => 'string', 'format' => 'YYYY/YYYY', 'enum' => null, 'group' => null],
            ['key' => 'periode_pembukuan', 'header' => 'Periode Pembukuan', 'required' => true, 'type' => 'string', 'format' => 'YYYY / YYYY', 'enum' => null, 'group' => null],
            ['key' => 'tanggal_mulai_tagihan', 'header' => 'Tanggal Mulai Tagihan', 'required' => true, 'type' => 'datetime', 'format' => 'mm-dd-yy', 'enum' => null, 'group' => null],
            ['key' => 'tanggal_jatuh_tempo_tagihan', 'header' => 'Tanggal Jatuh Tempo Tagihan', 'required' => true, 'type' => 'datetime', 'format' => 'mm-dd-yy', 'enum' => null, 'group' => null],
            ['key' => 'tipe_pos_tagihan', 'header' => 'Tipe POS Tagihan', 'required' => true, 'type' => 'string', 'format' => null, 'enum' => ['SPP', 'DPP', 'Lainnya'], 'group' => null],
            ['key' => 'nama_pos', 'header' => 'Nama POS (Jika memilih Lainnya)', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
            ['key' => 'bulan', 'header' => 'Bulan', 'required' => true, 'type' => 'datetime', 'format' => 'mm/yyyy', 'enum' => null, 'group' => null],
            ['key' => 'tipe_angsuran', 'header' => 'Tipe Angsuran', 'required' => true, 'type' => 'string', 'format' => null, 'enum' => ['Bisa Diangsur', 'Tidak Bisa Diangsur'], 'group' => null],
            ['key' => 'catatan', 'header' => 'Catatan', 'required' => false, 'type' => 'string', 'format' => null, 'enum' => null, 'group' => null],
        ];
    }

    // ─── Helpers ─────────────────────────────────────────────────────

    /**
     * Get columns for a given category.
     */
    public static function getColumns(string $category): array
    {
        return match ($category) {
            'pegawai' => self::pegawaiColumns(),
            'murid'   => self::muridColumns(),
            'tagihan' => self::tagihanColumns(),
            default   => throw new \InvalidArgumentException("Unknown category: {$category}"),
        };
    }

    /**
     * Get only the header names in order.
     *
     * @return string[]
     */
    public static function getHeaders(string $category): array
    {
        return array_map(
            fn(array $col): string => $col['header'],
            self::getColumns($category)
        );
    }

    /**
     * Get column keys in order.
     *
     * @return string[]
     */
    public static function getKeys(string $category): array
    {
        return array_map(
            fn(array $col): string => $col['key'],
            self::getColumns($category)
        );
    }

    /**
     * Available categories with labels.
     *
     * @return array<string, string>
     */
    public static function categories(): array
    {
        return [
            'pegawai' => 'Data Pegawai/Guru',
            'murid'   => 'Data Murid/Dapodik',
            'tagihan' => 'Data Tagihan',
        ];
    }

    /**
     * Get the sheet name for export.
     */
    public static function getSheetName(string $category): string
    {
        return match ($category) {
            'pegawai' => 'Daftar guru',
            'murid'   => 'Worksheet',
            'tagihan' => 'Sheet1',
            default   => 'Sheet1',
        };
    }

    /**
     * Get group headers for templates with multi-level headers (e.g., Murid).
     * Returns null if not applicable.
     *
     * @return array<int, array{label: string, start: int, end: int}>|null
     */
    public static function getGroupHeaders(string $category): ?array
    {
        if ($category !== 'murid') {
            return null;
        }

        return [
            ['label' => 'Data Ayah', 'start' => 29, 'end' => 34],   // AD-AI (0-indexed: 29-34)
            ['label' => 'Data Ibu', 'start' => 35, 'end' => 40],    // AJ-AO
            ['label' => 'Data Wali', 'start' => 41, 'end' => 46],   // AP-AU
        ];
    }
}

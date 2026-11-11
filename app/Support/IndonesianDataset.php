<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Custom Indonesian datasets for realistic dummy data generation.
 *
 * All data here is generic/fictional and does not reference real individuals.
 */
final class IndonesianDataset
{
    /** @return string[] */
    public static function cities(): array
    {
        return [
            'JAKARTA', 'SURABAYA', 'BANDUNG', 'MEDAN', 'SEMARANG',
            'MAKASSAR', 'PALEMBANG', 'TANGERANG', 'DEPOK', 'BEKASI',
            'MALANG', 'BOGOR', 'YOGYAKARTA', 'SOLO', 'DENPASAR',
            'BALIKPAPAN', 'PONTIANAK', 'BANJARMASIN', 'PADANG', 'MANADO',
            'PROBOLINGGO', 'KEDIRI', 'MOJOKERTO', 'BLITAR', 'MADIUN',
            'BATU', 'PASURUAN', 'JEMBER', 'SIDOARJO', 'GRESIK',
            'CIREBON', 'TASIKMALAYA', 'SUKABUMI', 'GARUT', 'PURWOKERTO',
            'TEGAL', 'PEKALONGAN', 'SALATIGA', 'KLATEN', 'TULUNGAGUNG',
        ];
    }

    /** @return string[] */
    public static function kelurahan(): array
    {
        return [
            'Sawojajar', 'Singosari', 'Lowokwaru', 'Blimbing', 'Klojen',
            'Sukun', 'Kedungkandang', 'Bunulrejo', 'Tlogomas', 'Dinoyo',
            'Sumbersari', 'Ketawanggede', 'Jatimulyo', 'Mojolangu', 'Purwantoro',
            'Tulusrejo', 'Pandanwangi', 'Arjowinangun', 'Mergosono', 'Cemorokandang',
            'Ngetal', 'Pogalan', 'Duwet', 'Karangsono', 'Tanjungrejo',
        ];
    }

    /** @return string[] */
    public static function kecamatan(): array
    {
        return [
            'Kedungkandang', 'Singosari', 'Lowokwaru', 'Blimbing', 'Klojen',
            'Sukun', 'Pakis', 'Tumpang', 'Jabung', 'Lawang',
            'Dau', 'Karangploso', 'Batu', 'Junrejo', 'Bumiaji',
            'Pogalan', 'Trenggalek', 'Tulungagung', 'Kediri', 'Jombang',
            'Gondanglegi', 'Kepanjen', 'Sumbermanjing', 'Dampit', 'Pagak',
        ];
    }

    /** @return string[] */
    public static function dusun(): array
    {
        return [
            'Sawojajar', 'Singosari', 'Krajan', 'Duwet', 'Karanganyar',
            'Sumber', 'Ngadiluwih', 'Tegalweru', 'Pakel', 'Ngrowo',
            '-',
        ];
    }

    /** @return string[] */
    public static function banks(): array
    {
        return ['BCA', 'BNI', 'BRI', 'MANDIRI', 'BSI', 'BTN'];
    }

    /** @return string[] */
    public static function religions(): array
    {
        return ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];
    }

    /** @return string[] */
    public static function jobs(): array
    {
        return [
            'Wiraswasta', 'PNS', 'Karyawan Swasta', 'Guru', 'Dokter',
            'Pedagang', 'Petani', 'Nelayan', 'TNI', 'POLRI',
            'Buruh', 'Pensiunan', 'Ibu Rumah Tangga', 'Tidak Bekerja',
            'Sopir', 'Montir', 'Tukang', 'Satpam', 'Ojek Online',
        ];
    }

    /** @return string[] */
    public static function schoolNames(): array
    {
        return [
            'SDN 1 Malang', 'SDN 2 Surabaya', 'SDN 3 Bandung', 'SDN 4 Jakarta',
            'SD IT Al-Ikhlas', 'SD Islam Terpadu Harapan', 'SD Muhammadiyah 1',
            'SMP Negeri 1 Malang', 'SMP Negeri 2 Surabaya', 'SMP Negeri 3 Bandung',
            'MTs Negeri 1 Malang', 'MTs Al-Hidayah', 'MI Darul Ulum',
            'SD Xaverius', 'SD Santo Yoseph', 'SD Kristen Petra',
        ];
    }

    /** @return string[] */
    public static function rombelNames(): array
    {
        return [
            'Kelas 7A', 'Kelas 7B', 'Kelas 7C', 'Kelas 7D',
            'Kelas 8A', 'Kelas 8B', 'Kelas 8C', 'Kelas 8D',
            'Kelas 9A', 'Kelas 9B', 'Kelas 9C', 'Kelas 9D',
            'Kelas 10 IPA 1', 'Kelas 10 IPA 2', 'Kelas 10 IPS 1',
            'Kelas 11 IPA 1', 'Kelas 11 IPA 2', 'Kelas 11 IPS 1',
            'Kelas 12 IPA 1', 'Kelas 12 IPA 2', 'Kelas 12 IPS 1',
        ];
    }

    /**
     * Custom POS names for Tagihan "Lainnya" type.
     *
     * @return string[]
     */
    public static function tagihanPosNames(): array
    {
        return [
            'BUKU', 'SERAGAM', 'KEGIATAN', 'UJIAN', 'EKSTRAKULIKULER',
            'PRAKTEK', 'WISATA', 'FOTO', 'RAPOR', 'INFAQ',
        ];
    }

    /**
     * Custom tagihan names by POS type.
     *
     * @return array<string, string[]>
     */
    public static function tagihanNames(): array
    {
        return [
            'SPP' => ['Tagihan SPP', 'SPP Bulanan', 'Iuran SPP'],
            'DPP' => ['Tagihan DPP', 'Dana Pengembangan Pendidikan', 'DPP Tahunan'],
            'Lainnya' => [
                'Tagihan Buku LKS', 'Tagihan Seragam', 'Tagihan Kegiatan',
                'Tagihan Ujian Praktek', 'Tagihan Ekstrakulikuler', 'Tagihan Wisata',
                'Tagihan Foto', 'Tagihan Rapor', 'Tagihan Infaq',
            ],
        ];
    }
}

<?php

namespace Tests\Unit;

use App\Enums\GenerateMode;
use App\Services\Generators\MuridGenerator;
use App\Services\Generators\PegawaiGenerator;
use App\Services\Generators\TagihanGenerator;
use App\Support\TemplateSchema;
use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;

class GeneratorsTest extends TestCase
{
    private $faker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = FakerFactory::create('id_ID');
    }

    public function test_pegawai_generator_produces_exact_51_columns_in_order(): void
    {
        $generator = new PegawaiGenerator($this->faker);
        $expectedHeaders = TemplateSchema::getHeaders('pegawai');
        $expectedKeys = TemplateSchema::getKeys('pegawai');

        $this->assertCount(51, $expectedHeaders);

        $rows = $generator->generate(5, GenerateMode::NORMAL);
        $this->assertCount(5, $rows);

        foreach ($rows as $row) {
            $this->assertEquals($expectedKeys, array_keys($row));
            $this->assertNotEmpty($row['nama']);
            $this->assertContains($row['jk'], ['L', 'P']);
            $this->assertNotEmpty($row['tempat_lahir']);
            $this->assertNotEmpty($row['tanggal_lahir']);
        }
    }

    public function test_murid_generator_produces_exact_72_columns_and_required_fields(): void
    {
        $generator = new MuridGenerator($this->faker);
        $expectedHeaders = TemplateSchema::getHeaders('murid');
        $expectedKeys = TemplateSchema::getKeys('murid');

        $this->assertCount(72, $expectedHeaders);

        $rows = $generator->generate(5, GenerateMode::NORMAL);
        $this->assertCount(5, $rows);

        foreach ($rows as $row) {
            $this->assertEquals($expectedKeys, array_keys($row));
            $this->assertNotEmpty($row['nama']);
            $this->assertContains($row['jenis_kelamin'], ['Laki - Laki', 'Perempuan']);
            $this->assertNotEmpty($row['tempat_lahir']);
            $this->assertNotNull($row['tanggal_lahir']);
            $this->assertMatchesRegularExpression('/^\d{2}\/\d{2}\/\d{4}$/', $row['tanggal_lahir']);
            // Data Ayah required birth date
            $this->assertNotNull($row['ayah_tanggal_lahir']);
            $this->assertMatchesRegularExpression('/^\d{2}\/\d{2}\/\d{4}$/', $row['ayah_tanggal_lahir']);
            // Data Ibu required birth date
            $this->assertNotNull($row['ibu_tanggal_lahir']);
            $this->assertMatchesRegularExpression('/^\d{2}\/\d{2}\/\d{4}$/', $row['ibu_tanggal_lahir']);
        }
    }

    public function test_tagihan_generator_produces_exact_12_columns_and_conditional_nama_pos(): void
    {
        $generator = new TagihanGenerator($this->faker);
        $expectedHeaders = TemplateSchema::getHeaders('tagihan');
        $expectedKeys = TemplateSchema::getKeys('tagihan');

        $this->assertCount(12, $expectedHeaders);

        $rows = $generator->generate(10, GenerateMode::NORMAL);
        $this->assertCount(10, $rows);

        foreach ($rows as $row) {
            $this->assertEquals($expectedKeys, array_keys($row));
            $this->assertNotEmpty($row['kode_tagihan']);
            $this->assertContains($row['tipe_pos_tagihan'], ['SPP', 'DPP', 'Lainnya']);
            $this->assertContains($row['tipe_angsuran'], ['Bisa Diangsur', 'Tidak Bisa Diangsur']);

            if ($row['tipe_pos_tagihan'] === 'Lainnya') {
                $this->assertNotEmpty($row['nama_pos']);
            } else {
                $this->assertEmpty($row['nama_pos']);
            }
        }
    }
}

<?php

namespace Tests\Unit;

use App\Support\IdentifierGenerator;
use PHPUnit\Framework\TestCase;

class IdentifierGeneratorTest extends TestCase
{
    public function test_nik_length_is_16_digits(): void
    {
        $nik = IdentifierGenerator::nik();
        $this->assertEquals(16, strlen($nik));
        $this->assertMatchesRegularExpression('/^\d{16}$/', $nik);
    }

    public function test_no_kk_length_is_16_digits(): void
    {
        $noKk = IdentifierGenerator::noKk();
        $this->assertEquals(16, strlen($noKk));
        $this->assertMatchesRegularExpression('/^\d{16}$/', $noKk);
    }

    public function test_nisn_length_is_10_digits(): void
    {
        $nisn = IdentifierGenerator::nisn();
        $this->assertEquals(10, strlen($nisn));
        $this->assertMatchesRegularExpression('/^\d{10}$/', $nisn);
    }

    public function test_npwp_length_is_15_digits(): void
    {
        $npwp = IdentifierGenerator::npwp();
        $this->assertEquals(15, strlen($npwp));
        $this->assertMatchesRegularExpression('/^\d{15}$/', $npwp);
    }

    public function test_virtual_account_starts_with_valid_prefix(): void
    {
        $va = IdentifierGenerator::virtualAccount();
        $this->assertEquals(16, strlen($va));
        $this->assertMatchesRegularExpression('/^\d{16}$/', $va);
    }
}

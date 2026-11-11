<?php

namespace App\Services;

use App\Enums\GenerateMode;
use App\Services\Generators\AbstractGenerator;
use App\Services\Generators\MuridGenerator;
use App\Services\Generators\PegawaiGenerator;
use App\Services\Generators\TagihanGenerator;
use App\Support\TemplateSchema;
use Faker\Factory as FakerFactory;

// Service utama buat handle generate data dummy Dapodik
class TestDataGeneratorService
{
    // Fungsi untuk generate data sesuai template dan jumlah barisnya
    public function generate(string $category, int $quantity, GenerateMode $mode = GenerateMode::NORMAL, array $options = []): array
    {
        $generator = $this->resolveGenerator($category, $options);

        $data = $generator->generate($quantity, $mode);

        return [
            'headers' => $generator->getHeaders(),
            'keys' => $generator->getKeys(),
            'data' => $data,
            'category' => $category,
            'quantity' => $quantity,
            'mode' => $mode->value,
        ];
    }

    /**
     * Resolve the appropriate generator for a category.
     *
     * @param array<string, mixed> $options
     */
    private function resolveGenerator(string $category, array $options = []): AbstractGenerator
    {
        $faker = FakerFactory::create('id_ID');

        if (str_starts_with($category, 'custom_')) {
            $id = (int) substr($category, 7);
            $customTemplate = \App\Models\CustomTemplate::findOrFail($id);
            return new \App\Services\Generators\CustomTemplateGenerator($customTemplate, $faker);
        }

        return match ($category) {
            'pegawai' => new PegawaiGenerator($faker),
            'murid'   => new MuridGenerator($faker),
            'tagihan' => new TagihanGenerator(
                $faker,
                $options['tahun_ajaran'] ?? '',
                $options['periode_pembukuan'] ?? ($options['tahun_ajaran'] ?? '')
            ),
            default   => throw new \InvalidArgumentException("Unknown category: {$category}"),
        };
    }
}

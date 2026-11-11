<?php

namespace App\Services\Generators;

use App\Enums\GenerateMode;
use App\Models\CustomTemplate;
use App\Support\IndonesianDataset;
use Faker\Generator as Faker;

// Generator untuk template kustom yang dibuat atau diupload user
class CustomTemplateGenerator extends AbstractGenerator
{
    private CustomTemplate $template;
    private array $columns;

    public function __construct(CustomTemplate $template, Faker $faker)
    {
        parent::__construct($faker);
        $this->template = $template;
        $this->columns = $template->columns ?? [];
    }

    protected function category(): string
    {
        return 'custom_' . $this->template->id;
    }

    public function getSchema(): array
    {
        return $this->columns;
    }

    public function getHeaders(): array
    {
        return array_map(fn($col) => $col['header'] ?? $col['key'], $this->columns);
    }

    public function getKeys(): array
    {
        return array_map(fn($col) => $col['key'], $this->columns);
    }

    protected function generateRow(int $index, GenerateMode $mode): array
    {
        $row = [];
        $isEdge = $mode === GenerateMode::EDGE_CASE || $mode->value === 'edge_cases';

        foreach ($this->columns as $col) {
            $key = $col['key'];
            $type = $col['type'] ?? 'text';
            $required = $col['required'] ?? true;

            // In edge cases mode, occasionally produce empty string for non-strictly required columns
            if ($isEdge && !$required && $this->faker->boolean(25)) {
                $row[$key] = '';
                continue;
            }

            $row[$key] = $this->generateFieldValue($type, $index, $isEdge);
        }

        return $row;
    }

    private function generateFieldValue(string $type, int $index, bool $isEdge): mixed
    {
        return match ($type) {
            'number' => $index,

            'name' => $isEdge && $this->faker->boolean(20)
                ? $this->faker->randomElement([
                    "Muhammad Nur Al-Fatih, S.Pd., M.Pd.",
                    "Siti Nur'aini binti Abdul Malik",
                    "Anastasia D'Souza S.",
                    "Tubagus Raden Roro Sri Kusuma Dewi Kusumaningrat",
                ])
                : $this->faker->name(),

            'nik' => $isEdge && $this->faker->boolean(15)
                ? '357801' . str_pad((string)$this->faker->numberBetween(1, 9999999999), 10, '0', STR_PAD_LEFT)
                : '3578' . $this->faker->numerify('############'),

            'nisn' => str_pad((string) $this->faker->numberBetween(1000000000, 9999999999), 10, '0', STR_PAD_LEFT),

            'nuptk' => $this->faker->numerify('################'),

            'nip' => $this->faker->numerify('1990########1###'),

            'gender' => $this->faker->randomElement(['L', 'P']),

            'birth_place' => $this->faker->randomElement(IndonesianDataset::cities()),

            'date' => $this->faker->dateTimeBetween('-35 years', '-1 years')->format('Y-m-d'),

            'email' => $isEdge && $this->faker->boolean(20)
                ? 'user.' . $this->faker->lexify('????') . '+testing@sub.domain.sch.id'
                : $this->faker->unique(false, 5000)->safeEmail(),

            'phone' => '08' . $this->faker->randomElement(['12', '13', '21', '52', '56', '77', '81', '96']) . $this->faker->numerify('########'),

            'address' => 'Jl. ' . $this->faker->streetName() . ' No. ' . $this->faker->numberBetween(1, 150),

            'city' => $this->faker->randomElement(IndonesianDataset::cities()),

            'province' => $this->faker->randomElement([
                'Jawa Timur', 'Jawa Barat', 'Jawa Tengah', 'DKI Jakarta', 'DI Yogyakarta',
                'Banten', 'Bali', 'Sumatera Utara', 'Sumatera Selatan', 'Sulawesi Selatan',
            ]),

            'postal_code' => $this->faker->numerify('6####'),

            'currency' => $this->faker->numberBetween(10, 250) * 10000,

            'boolean' => $this->faker->randomElement(['Aktif', 'Nonaktif']),

            default => $isEdge && $this->faker->boolean(20)
                ? $this->faker->sentence(8) . ' (Catatan Khusus: "Valid & Lulus")'
                : $this->faker->words(3, true),
        };
    }
}

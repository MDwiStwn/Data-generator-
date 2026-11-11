<?php

namespace App\Services\Generators;

use App\Enums\GenerateMode;
use App\Support\TemplateSchema;
use Faker\Generator as Faker;

// Class induk buat semua generator data
abstract class AbstractGenerator
{
    protected Faker $faker;

    public function __construct(Faker $faker)
    {
        $this->faker = $faker;
    }

    /**
     * The template category name (pegawai, murid, tagihan).
     */
    abstract protected function category(): string;

    /**
     * Generate a single row of data.
     *
     * @param int          $index 1-based row index
     * @param GenerateMode $mode  Generation mode
     * @return array<string, mixed> Keyed by column key
     */
    abstract protected function generateRow(int $index, GenerateMode $mode): array;

    /**
     * Get the schema columns for this generator's template.
     */
    public function getSchema(): array
    {
        return TemplateSchema::getColumns($this->category());
    }

    /**
     * Get headers in order.
     *
     * @return string[]
     */
    public function getHeaders(): array
    {
        return TemplateSchema::getHeaders($this->category());
    }

    /**
     * Get column keys in order.
     *
     * @return string[]
     */
    public function getKeys(): array
    {
        return TemplateSchema::getKeys($this->category());
    }

    /**
     * Generate a batch of data rows.
     *
     * @param int          $quantity Number of rows to generate
     * @param GenerateMode $mode     Generation mode
     * @return array<int, array<string, mixed>>
     */
    public function generate(int $quantity, GenerateMode $mode = GenerateMode::NORMAL): array
    {
        $rows = [];

        for ($i = 1; $i <= $quantity; $i++) {
            $row = $this->generateRow($i, $mode);

            // Ensure all columns exist in the correct order
            $orderedRow = [];
            foreach ($this->getKeys() as $key) {
                $orderedRow[$key] = $row[$key] ?? '';
            }

            $rows[] = $orderedRow;
        }

        return $rows;
    }

    /**
     * Pick a random value from an enum array, optionally including empty string.
     */
    protected function randomEnum(array $values, bool $allowEmpty = false): string
    {
        if ($allowEmpty && $this->faker->boolean(30)) {
            return '';
        }

        return $values[array_rand($values)];
    }

    /**
     * Conditionally generate a value or return empty string.
     *
     * @param float    $probability Probability (0-100) of generating a value
     * @param callable $generator   Function that returns the value
     * @return mixed
     */
    protected function maybe(float $probability, callable $generator): mixed
    {
        if ($this->faker->boolean((int) $probability)) {
            return $generator();
        }

        return '';
    }
}

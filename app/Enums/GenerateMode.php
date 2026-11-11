<?php

declare(strict_types=1);

namespace App\Enums;

enum GenerateMode: string
{
    case NORMAL = 'normal';
    case RANDOM = 'random';
    case EDGE_CASE = 'edge_case';
    case INVALID = 'invalid';
    case DUPLICATE = 'duplicate';

    public function label(): string
    {
        return match ($this) {
            self::NORMAL => 'Normal Data',
            self::RANDOM => 'Random Data',
            self::EDGE_CASE => 'Edge Case',
            self::INVALID => 'Invalid Data',
            self::DUPLICATE => 'Duplicate Data',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::NORMAL => 'Menghasilkan data dummy yang valid dan realistis.',
            self::RANDOM => 'Menghasilkan variasi data dummy secara acak.',
            self::EDGE_CASE => 'Menghasilkan data untuk menguji batas input.',
            self::INVALID => 'Menghasilkan data yang sengaja tidak valid.',
            self::DUPLICATE => 'Menghasilkan data dengan identifier yang sama.',
        };
    }

    /**
     * Modes available for MVP.
     *
     * @return array<self>
     */
    public static function availableModes(): array
    {
        return [
            self::NORMAL,
            self::RANDOM,
        ];
    }
}

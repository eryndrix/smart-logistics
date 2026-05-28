<?php declare(strict_types=1);

namespace App\Shared;

use WendellAdriel\ValidatedDTO\Concerns\EmptyCasts;
use WendellAdriel\ValidatedDTO\SimpleDTO;

abstract class Command extends SimpleDTO
{
    /**
     * Enables support for empty casts.
     */
    use EmptyCasts;

    /**
     * @phpstan-return array<string, mixed>
     */
    protected function rules(): array
    {
        return [];
    }

    /**
     * @phpstan-return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [];
    }

    /**
     * @phpstan-return array<string, mixed>
     */
    protected function casts(): array
    {
        return [];
    }
}

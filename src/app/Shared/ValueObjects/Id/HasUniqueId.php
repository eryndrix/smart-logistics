<?php declare(strict_types=1);

namespace App\Shared\ValueObjects\Id;

use App\Shared\Casts\IdCast;

/**
 * @phpstan-template TUniqueId of UniqueId
 */
trait HasUniqueId
{
    /**
     * @phpstan-param non-empty-string $id
     * @phpstan-return TUniqueId
     */
    protected static function make(string $id): static
    {
        return new static(id: $id);
    }

    /**
     * @phpstan-return class-string<
     *     \Illuminate\Contracts\Database\Eloquent\CastsAttributes<
     *         UniqueId,
     *         string|UniqueId
     *     >|\Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes
     * >
     */
    protected static function castClassName(): string
    {
        return IdCast::class;
    }
}

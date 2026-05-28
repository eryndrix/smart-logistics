<?php declare(strict_types=1);

namespace App\Shared\ValueObjects\Id;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Support\Str;

abstract class UniqueId implements Castable
{
    /**
     * @phpstan-var non-empty-string
     */
    private string $id;

    /**
     * @phpstan-param non-empty-string $id
     * @throws \InvalidArgumentException
     */
    protected function __construct(string $id)
    {
        if (!Str::isUuid(value: trim(string: $id), version: 7)) {
            throw new \InvalidArgumentException(
                message: sprintf(
                    "Value '%s' is not a valid UUID v7.",
                    $id
                )
            );
        }

        $this->id = $id;
    }

    /**
     * @internal
     *
     * @phpstan-param non-empty-string $id
     * @phpstan-return static
     */
    abstract protected static function make(string $id): static;

    /**
     * @phpstan-param non-empty-string $value
     * @phpstan-return static
     */
    public static function of(string $value): static
    {
        return static::make(id: $value);
    }

    /**
     * @phpstan-return static
     */
    public static function generate(): static
    {
        return static::make(id: Str::uuid7()->toString());
    }

    /**
     * @phpstan-param self $other
     * @phpstan-return bool
     */
    public function equals(self $other): bool
    {
        return $this->id === $other->id;
    }

    /**
     * @phpstan-return class-string<
     *     \Illuminate\Contracts\Database\Eloquent\CastsAttributes<
     *         UniqueId,
     *         string|UniqueId
     *     >|\Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes
     * >
     */
    abstract protected static function castClassName(): string;

    /**
     * @phpstan-param array<string> $arguments
     *
     * @phpstan-return class-string<
     *     \Illuminate\Contracts\Database\Eloquent\CastsAttributes<
     *         UniqueId,
     *         string|UniqueId
     *     >|\Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes
     * >
     */
    public static function castUsing(array $arguments): string
    {
        return static::castClassName();
    }

    /**
     * @phpstan-return non-empty-string
     */
    public function asString(): string
    {
        return $this->id;
    }

    /**
     * @phpstan-return non-empty-string
     */
    public function __toString(): string
    {
        return $this->asString();
    }
}

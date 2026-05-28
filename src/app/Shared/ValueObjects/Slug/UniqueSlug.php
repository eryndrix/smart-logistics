<?php declare(strict_types=1);

namespace App\Shared\ValueObjects\Slug;

use Illuminate\Contracts\Database\Eloquent\Castable;

abstract class UniqueSlug implements Castable
{
    /**
     * @phpstan-var non-empty-string
     */
    private string $slug;

    /**
     * @phpstan-param non-empty-string $slug
     * @throws \InvalidArgumentException
     */
    protected function __construct(string $slug)
    {
        $slug = trim(string: $slug);

        if ($slug === '') {
            throw new \InvalidArgumentException(
                message: 'Slug value cannot be empty.'
            );
        }

        $this->slug = $slug;
    }

    /**
     * @internal
     *
     * @phpstan-param non-empty-string $slug
     * @phpstan-return static
     */
    abstract protected static function make(string $slug): static;

    /**
     * @phpstan-param string $value
     * @phpstan-return static
     */
    public static function of(string $value): static
    {
        $slug = trim(string: $value);
        $slug = $slug === '' ? 'unknown' : $slug;

        return static::make(slug: $slug);
    }

    /**
     * @phpstan-param string $value
     * @phpstan-return static
     */
    public static function generate(string $value): static
    {
        $slug = trim(string: $value);
        $slug = $slug === '' ? 'unknown' : $slug;

        return static::make(slug: $slug);
    }

    /**
     * @phpstan-param static $other
     *
     * @phpstan-return bool
     */
    public function equals(self $other): bool
    {
        return $this->slug === $other->slug;
    }

    /**
     *
     * @phpstan-return class-string<
     *     \Illuminate\Contracts\Database\Eloquent\CastsAttributes<
     *         UniqueSlug,
     *         string|UniqueSlug
     *     >|\Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes
     * >
     */
    abstract protected static function castClassName(): string;

    /**
     * @phpstan-param array<string> $arguments
     *
     * @phpstan-return class-string<
     *     \Illuminate\Contracts\Database\Eloquent\CastsAttributes<
     *         UniqueSlug,
     *         string|UniqueSlug
     *     >|\Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes
     * >
     */
    public static function castUsing(array $arguments): string
    {
        return static::castClassName();
    }

    /**
     * @phpstan-return string
     */
    public function asString(): string
    {
        return $this->slug;
    }

    /**
     * @phpstan-return string
     */
    public function __toString(): string
    {
        return $this->asString();
    }
}

<?php declare(strict_types=1);

namespace App\Shared\ValueObjects\Slug;

use App\Shared\Casts\SlugCast;

final class RoleSlug extends UniqueSlug
{
    /**
     * @phpstan-param non-empty-string $slug
     */
    protected function __construct(string $slug)
    {
        parent::__construct(slug: $slug);

        if (strlen(string: $slug) < 3
            || strlen(string: $slug) > 20
        ) {
            throw new \InvalidArgumentException(
                message: 'Role slug length 2-50 chars.'
            );
        }
    }

    /**
     * @phpstan-param non-empty-string $slug
     *
     * @phpstan-return self
     */
    protected static function make(string $slug): self
    {
        return new self(slug: $slug);
    }

    /**
     * @phpstan-return class-string<
     *     \Illuminate\Contracts\Database\Eloquent\CastsAttributes<
     *         UniqueSlug,
     *         string|UniqueSlug
     *     >|\Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes
     * >
     */
    protected static function castClassName(): string
    {
        return SlugCast::class;
    }
}

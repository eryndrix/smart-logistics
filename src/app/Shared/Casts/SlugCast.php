<?php declare(strict_types=1);

namespace App\Shared\Casts;

use App\Shared\ValueObjects\Slug\UniqueSlug;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * @phpstan-implements CastsAttributes<UniqueSlug, string|UniqueSlug>
 */
final class SlugCast implements CastsAttributes
{
    /**
     * @phpstan-param Model $model
     * @phpstan-param string $key
     * @phpstan-param mixed $value
     * @phpstan-param array<string,mixed> $attributes
     *
     * @phpstan-return UniqueSlug|null
     * 
     * @throws \RuntimeException
     */
    public function get(
        Model $model,
        string $key,
        mixed $value,
        array $attributes
    ): ?UniqueSlug {
        if ($value === null) {
            return null;
        }

        if (!is_string(value: $value) || $value === '') {
            throw new \RuntimeException(
                message: 'Slug value is not a string or null.'
            );
        }

        $slugClass = $this->resolveSlugClass(
            modelClass: $model::class
        );

        /** @phpstan-var UniqueSlug $uniqueSlug */
        $uniqueSlug = $slugClass::of(value: $value);

        return $uniqueSlug;
    }

    /**
     * @phpstan-param Model $model
     * @phpstan-param string $key
     * @phpstan-param mixed $value
     * @phpstan-param array<string,mixed> $attributes
     *
     * @phpstan-return string
     * 
     * @throws \RuntimeException
     */
    public function set(
        Model $model,
        string $key,
        mixed $value,
        array $attributes
    ): string {
        if ($value === null) {
            return '';
        }

        if ($value instanceof UniqueSlug) {
            return (string) $value;
        }

        if (!is_string(value: $value) || $value === '') {
            throw new \RuntimeException(
                message: 'Slug must be a string, UniqueSlug or null.'
            );
        }

        $slugClass = $this->resolveSlugClass(
            modelClass: $model::class
        );

        /** @phpstan-var UniqueSlug $uniqueSlug */
        $uniqueSlug = $slugClass::of(value: $value);

        return (string) $uniqueSlug;
    }

    /**
     * @phpstan-param class-string<Model> $modelClass
     * @phpstan-return class-string<UniqueSlug>
     */
    private function resolveSlugClass(string $modelClass): string
    {
        /**
         * @phpstan-var array<
         *     class-string<Model>,
         *     class-string<UniqueSlug>
         * > $map */
        $map = config(key: 'casts.slug', default: []);

        return $map[$modelClass]
            ?? throw new \RuntimeException(
                message: 'Slug mapping missing for: ' . $modelClass
            );
    }
}

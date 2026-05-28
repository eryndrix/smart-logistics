<?php declare(strict_types=1);

namespace App\Shared\Casts;

use App\Shared\ValueObjects\Id\UniqueId;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * @phpstan-implements CastsAttributes<UniqueId, string|UniqueId>
 */
final class IdCast implements CastsAttributes
{
    /**
     * @phpstan-param Model $model
     * @phpstan-param string $key
     * @phpstan-param mixed $value
     * @phpstan-param array<string,mixed> $attributes
     *
     * @phpstan-return UniqueId|null
     * 
     * @throws \RuntimeException
     */
    public function get(
        Model $model,
        string $key,
        mixed $value,
        array $attributes
    ): ?UniqueId {
        if ($value === null) {
            return null;
        }

        if (!is_string(value: $value) || $value === '') {
            throw new \RuntimeException(
                message: 'ID value must be a non-empty string or null.'
            );
        }

        $class = $this->resolve(modelClass: $model::class);
        return $class::of(value: $value);
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

        if ($value instanceof UniqueId) {
            return (string) $value;
        }

        if (!is_string(value: $value) || $value === '') {
            throw new \RuntimeException(
                message: 'ID must be a non-empty string, UniqueId or null.'
            );
        }

        $class = $this->resolve(modelClass: $model::class);
        return (string) $class::of(value: $value);
    }

    /**
     * @phpstan-param class-string<Model> $modelClass
     * @phpstan-return class-string<UniqueId>
     * 
     * @throws \RuntimeException
     */
    private function resolve(string $modelClass): string
    {
        /**
         * @phpstan-var array<
         *     class-string<Model>,
         *     class-string<UniqueId>
         * > $map */
        $map = config(key: 'casts.id', default: []);

        return $map[$modelClass]
            ?? throw new \RuntimeException(
                message: 'ID mapping missing for: ' . $modelClass
            );
    }
}

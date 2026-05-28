<?php declare(strict_types=1);

namespace App\Shared\Casts;

use App\Shared\ValueObjects\Phone;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * @phpstan-implements CastsAttributes<Phone, non-empty-string>
 */
final class PhoneCast implements CastsAttributes
{
    /**
     * @phpstan-param Model $model
     * @phpstan-param string $key
     * @phpstan-param mixed $value
     * @phpstan-param array<string,mixed> $attributes
     *
     * @phpstan-return Phone
     * 
     * @throws \UnexpectedValueException
     */
    public function get(
        Model $model,
        string $key,
        mixed $value,
        array $attributes
    ): Phone {
        if (!is_string(value: $value) || $value === '') {
            throw new \UnexpectedValueException(
                message: 'Phone attribute must be a non-empty string.'
            );
        }

        return Phone::of(value: $value);
    }

    /**
     * @phpstan-param Model $model
     * @phpstan-param string $key
     * @phpstan-param mixed $value
     * @phpstan-param array<string,mixed> $attributes
     *
     * @phpstan-return non-empty-string
     * 
     * @throws \UnexpectedValueException
     */
    public function set(
        Model $model,
        string $key,
        mixed $value,
        array $attributes
    ): string {
        if ($value instanceof Phone) {
            return (string) $value;
        }

        if (!is_string(value: $value) || $value === '') {
            throw new \UnexpectedValueException(
                message: 'Phone attribute must be a non-empty string.'
            );
        }

        return Phone::of(value: $value)->value();
    }
}

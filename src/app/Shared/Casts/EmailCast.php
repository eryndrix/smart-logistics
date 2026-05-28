<?php declare(strict_types=1);

namespace App\Shared\Casts;

use App\Shared\ValueObjects\Email;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * @phpstan-implements CastsAttributes<Email, non-empty-string>
 */
final class EmailCast implements CastsAttributes
{
    /**
     * @phpstan-param Model $model
     * @phpstan-param string $key
     * @phpstan-param mixed $value
     * @phpstan-param array<string,mixed> $attributes
     *
     * @phpstan-return Email
     * 
     * @throws \UnexpectedValueException
     */
    public function get(
        Model $model,
        string $key,
        mixed $value,
        array $attributes
    ): Email {
        if (!is_string(value: $value) || $value === '') {
            throw new \UnexpectedValueException(
                message: 'Email attribute must be a non-empty string.'
            );
        }

        return Email::of(value: $value);
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
        if ($value instanceof Email) {
            return (string) $value;
        }

        if (!is_string(value: $value) || $value === '') {
            throw new \UnexpectedValueException(
                message: 'Email attribute must be a non-empty string.'
            );
        }

        return Email::of(value: $value)->value();
    }
}

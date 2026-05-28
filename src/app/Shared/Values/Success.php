<?php declare(strict_types=1);

namespace App\Shared\Values;

use App\Shared\Result;

/**
 * @phpstan-template TValue
 * @phpstan-template-covariant TError
 * @phpstan-extends Result<TValue, TError>
 */
final class Success extends Result
{
    /**
     * @phpstan-param TValue $value
     */
    public function __construct(mixed $value)
    {
        parent::__construct(value: $value);
    }

    /**
     * @phpstan-return bool
     */
    public function isSuccess(): bool
    {
        return true;
    }
}

<?php declare(strict_types=1);

namespace App\Shared\Values;

use App\Shared\Result;

/**
 * @phpstan-template TValue
 * @phpstan-template-covariant TError
 * @phpstan-extends Result<TValue, TError>
 */
final class Failure extends Result
{
    /**
     * @phpstan-param TError $error
     */
    public function __construct(mixed $error)
    {
        parent::__construct(error: $error);
    }

    /**
     * @phpstan-return bool
     */
    public function isSuccess(): bool
    {
        return false;
    }
}

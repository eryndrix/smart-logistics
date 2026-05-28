<?php declare(strict_types=1);

namespace App\Shared;

use App\Shared\Values\Failure;
use App\Shared\Values\MapHelpers;
use App\Shared\Values\Success;

/**
 * @phpstan-template TValue
 * @phpstan-template-covariant TError
 */
abstract class Result
{
    /** @phpstan-use MapHelpers<TValue, TError> */
    use MapHelpers;

    /**
     * @phpstan-param TValue|null $value
     * @phpstan-param TError|null $error
     */
    protected function __construct(
        public readonly mixed $value = null,
        public readonly mixed $error = null
    ) {}

    /**
     * @template TNewValue
     * @phpstan-param TNewValue $value
     * @phpstan-return Success<TNewValue, never>
     */
    public static function success(mixed $value): Success
    {
        /** @phpstan-var Success<TNewValue, never> */
        return new Success(value: $value);
    }

    /**
     * @template TNewError
     * @phpstan-param TNewError $error
     * @phpstan-return Failure<never, TNewError>
     */
    public static function failure(mixed $error): Failure
    {
        /** @phpstan-var Failure<never, TNewError> */
        return new Failure(error: $error);
    }

    /**
     * @phpstan-return bool
     */
    abstract public function isSuccess(): bool;

    /**
     * @phpstan-return bool
     */
    public function isFailure(): bool
    {
        return !$this->isSuccess();
    }
}

<?php declare(strict_types=1);

namespace App\Shared\Values;

use App\Shared\Result;

/**
 * @phpstan-template TSuccess
 * @phpstan-template-covariant TError
 */
trait MapHelpers
{
    /**
     * @template TNewSuccess
     * @phpstan-param callable(TSuccess): TNewSuccess $mapper
     * @phpstan-return Result<TNewSuccess, TError>
     */
    public function map(callable $mapper): self
    {
        if (!$this->isSuccess()) {
            return $this;
        }

        if ($this->value === null) {
            return $this;
        }

        return self::success(value: $mapper($this->value));
    }

    /**
     * @template TNewError
     * @phpstan-param callable(TError): TNewError $mapper
     * @phpstan-return Result<TSuccess, TNewError>
     */
    public function mapError(callable $mapper): self
    {
        if (!$this->isFailure()) {
            return $this;
        }

        if ($this->error === null) {
            return $this;
        }

        return self::failure(error: $mapper($this->error));
    }

    /**
     * @phpstan-param callable(TSuccess): mixed $onSuccess
     * @phpstan-param callable(TError): mixed $onError
     *
     * @phpstan-return mixed
     */
    public function match(
        callable $onSuccess, callable $onError): mixed
    {
        if ($this->isSuccess()) {
            /** @phpstan-var TSuccess $value */
            $value = $this->value;
            return $onSuccess($value);
        }

        /** @phpstan-var TError $error */
        $error = $this->error;
        return $onError($error);
    }
}

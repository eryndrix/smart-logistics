<?php declare(strict_types=1);

namespace App\Domains\Message\Repositories\Lock;

/**
 * @phpstan-template TKey of string
 */
interface MessageLockRepositoryInterface
{
    /**
     * @phpstan-param TKey $key
     * @phpstan-return bool
     */
    public function acquire(string $key): bool;

    /**
     * @phpstan-param TKey $key
     * @phpstan-return bool
     */
    public function check(string $key): bool;

    /**
     * @phpstan-param TKey $key
     * @phpstan-return void
     */
    public function mark(string $key): void;

    /**
     * @phpstan-param TKey $key
     * @phpstan-return void
     */
    public function release(string $key): void;
}

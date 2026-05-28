<?php declare(strict_types=1);

namespace App\Domains\Message\Repositories\Lock;

/**
 * @phpstan-implements MessageLockRepositoryInterface<string>
 */
final class MessageLockRepository implements
    MessageLockRepositoryInterface
{
    /**
     * @phpstan-param MessageLockMemoryRepository $memory
     * @phpstan-param MessageLockStorageRepository $storage
     */
    public function __construct(
        private MessageLockMemoryRepository $memory,
        private MessageLockStorageRepository $storage
    ) {}

    /**
     * @phpstan-param string $key
     * @phpstan-return bool
     */
    public function acquire(string $key): bool
    {
        if ($this->memory->check(key: $key)) {
            return false;
        }

        if (!$this->memory->acquire(key: $key)) {
            return false;
        }

        $this->storage->acquire(key: $key);

        return true;
    }

    /**
     * @phpstan-param string $key
     * @phpstan-return bool
     */
    public function check(string $key): bool
    {
        if ($this->memory->check(key: $key)) {
            return true;
        }

        return $this->storage->check(key: $key);
    }

    /**
     * @phpstan-param string $key
     * @phpstan-return void
     */
    public function mark(string $key): void
    {
        $this->memory->mark(key: $key);
        $this->storage->mark(key: $key);
    }

    /**
     * @phpstan-param string $key
     * @phpstan-return void
     */
    public function release(string $key): void
    {
        $this->memory->release(key: $key);
        $this->storage->release(key: $key);
    }
}

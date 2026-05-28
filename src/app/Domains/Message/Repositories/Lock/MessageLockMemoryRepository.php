<?php declare(strict_types=1);

namespace App\Domains\Message\Repositories\Lock;

use Illuminate\Support\Facades\Redis;

/**
 * @phpstan-implements MessageLockRepositoryInterface<string>
 */
final class MessageLockMemoryRepository implements MessageLockRepositoryInterface
{
    /**
     * @phpstan-return string
     */
    public const PROCESSING_STATUS = 'processing:';

    /**
     * @phpstan-return int
     */
    public const PROCESSING_TTL = 300;

    /**
     * @phpstan-return string
     */
    public const PROCESSED_STATUS = 'processed:';

    /**
     * @phpstan-return int
     */
    public const PROCESSED_TTL = 900;

    /**
     * @phpstan-param string $key
     * @phpstan-return bool
     */
    public function acquire(string $key): bool
    {
        /** @phpstan-ignore-next-line */
        $result = Redis::connection()->set(
            self::PROCESSING_STATUS . $key,
            now()->toIso8601String(),
            'EX',
            self::PROCESSING_TTL,
            'NX'
        );

        return $result === true;
    }

    /**
     * @phpstan-param string $key
     * @phpstan-return bool
     */
    public function check(string $key): bool
    {
        // @phpstan-ignore-next-line
        return Redis::connection()->exists(
            key: self::PROCESSED_STATUS . $key
        ) > 0;
    }

    /**
     * @phpstan-param string $key
     *
     * @phpstan-return void
     */
    public function mark(string $key): void
    {
        /** @phpstan-ignore-next-line */
        Redis::connection()->set(
            self::PROCESSED_STATUS . $key,
            now()->toIso8601String(),
            'EX',
            self::PROCESSED_TTL,
            'NX'
        );

        // @phpstan-ignore-next-line
        Redis::connection()->del(
            key: self::PROCESSING_STATUS . $key
        );
    }

    /**
     * @phpstan-param string $key
     *
     * @phpstan-return void
     */
    public function release(string $key): void
    {
        // @phpstan-ignore-next-line
        Redis::connection()->del(
            key: self::PROCESSING_STATUS . $key
        );
    }
}

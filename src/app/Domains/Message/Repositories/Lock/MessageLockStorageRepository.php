<?php declare(strict_types=1);

namespace App\Domains\Message\Repositories\Lock;

use Illuminate\Support\Facades\DB;
use Carbon\CarbonImmutable;

/**
 * @phpstan-implements MessageLockRepositoryInterface<string>
 */
final class MessageLockStorageRepository implements MessageLockRepositoryInterface
{
    /**
     * @phpstan-return string
     */
    public const string PROCESSING_STATUS = 'processing';

    /**
     * @phpstan-return int
     */
    public const int PROCESSING_TTL = 300;

    /**
     * @phpstan-return string
     */
    public const string PROCESSED_STATUS = 'processed';

    /**
     * @phpstan-return int
     */
    public const int PROCESSED_TTL = 86400;

    /**
     * @phpstan-param string $key
     * @phpstan-return bool
     */
    public function acquire(string $key): bool
    {
        $expiresAt = CarbonImmutable::now()->copy()->addSeconds(
            self::PROCESSING_TTL
        );

        return DB::table(
            table: 'message_locks'
        )->insertOrIgnore(
            values: [
                'fingerprint' => $key,
                'status' => self::PROCESSING_STATUS,
                'expires_at' => $expiresAt,
                'created_at' => CarbonImmutable::now(),
                'updated_at' => CarbonImmutable::now(),
            ]
        ) === 1;
    }

    /**
     * @phpstan-param string $key
     * @phpstan-return bool
     */
    public function check(string $key): bool
    {
        return DB::table(
            table: 'message_locks'
        )->where(
            column: 'fingerprint',
            operator: '=',
            value: $key
        )->where(
            column: 'expires_at',
            operator: '>',
            value: CarbonImmutable::now()
        )->exists();
    }

    /**
     * @phpstan-param string $key
     * @phpstan-return void
     */
    public function mark(string $key): void
    {
        DB::table(
            table: 'message_locks'
        )->where(
            column: 'fingerprint',
            operator: '=',
            value: $key
        )->update([
            'status' => self::PROCESSED_STATUS,
            'updated_at' => CarbonImmutable::now(),
        ]);
    }

    /**
     * @phpstan-param string $key
     * @phpstan-return void
     */
    public function release(string $key): void
    {
        DB::table(
            table: 'message_locks'
        )->where(
            column: 'fingerprint',
            operator: '=',
            value: $key
        )->delete();
    }
}

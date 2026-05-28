<?php declare(strict_types=1);

namespace App\Shared\Enums;

enum Status: string
{
    /**
     * Message is waiting to be processed.
     */
    case QUEUED = 'queued';

    /**
     * Message has been sent.
     */
    case SENT = 'sent';

    /**
     * Message has been delivered.
     */
    case DELIVERED = 'delivered';

    /**
     * Message delivery has failed.
     */
    case FAILED = 'failed';

    /**
     * @phpstan-return string
     */
    public function label(): string
    {
        return match ($this) {
            self::QUEUED => 'Queued',
            self::SENT => 'Sent',
            self::DELIVERED => 'Delivered',
            self::FAILED => 'Failed',
        };
    }

    /**
     * @phpstan-return bool
     */
    public function isQueued(): bool
    {
        return $this === self::QUEUED;
    }

    /**
     * @phpstan-return bool
     */
    public function isSent(): bool
    {
        return $this === self::SENT;
    }

    /**
     * @phpstan-return bool
     */
    public function isDelivered(): bool
    {
        return $this === self::DELIVERED;
    }

    /**
     * @phpstan-return bool
     */
    public function isFailed(): bool
    {
        return $this === self::FAILED;
    }
}

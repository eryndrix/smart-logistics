<?php declare(strict_types=1);

namespace App\Shared\Enums;

enum Priority: string
{
    /**
     * Urgent message type.
     */
    case URGENT = 'urgent';

    /**
     * Normal message type.
     */
    case NORMAL = 'normal';

    /**
     * @phpstan-return string
     */
    public function worker(): string
    {
        return match ($this) {
            self::URGENT => 'messages.high',
            self::NORMAL => 'messages.low',
        };
    }

    /**
     * @phpstan-return bool
     */
    public function isUrgent(): bool
    {
        return $this === self::URGENT;
    }

    /**
     * @phpstan-return bool
     */
    public function isNormal(): bool
    {
        return $this === self::NORMAL;
    }
}

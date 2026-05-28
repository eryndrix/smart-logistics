<?php declare(strict_types=1);

namespace App\Shared\Enums;

enum Channel: string
{
    /**
     * SMS channel identifier.
     */
    case SMS = 'sms';

    /**
     * Mail channel identifier.
     */
    case MAIL = 'mail';

    /**
     * @phpstan-return string
     */
    public function label(): string
    {
        return match ($this) {
            self::SMS => 'SMS',
            self::MAIL => 'Mail',
        };
    }

    /**
     * @phpstan-return bool
     */
    public function isSms(): bool
    {
        return $this === self::SMS;
    }

    /**
     * @phpstan-return bool
     */
    public function isMail(): bool
    {
        return $this === self::MAIL;
    }
}

<?php declare(strict_types=1);

namespace App\Domains\Message\Transports\Mail\Channels;

use App\Domains\Message\Transports\Mail\MailNotificationContract;
use App\Shared\ValueObjects\Email;

/**
 * @phpstan-template TEmail of Email
 * @phpstan-template TNotification of MailNotificationContract<TEmail>
 */
interface MailChannelContract
{
    /**
     * @phpstan-param TEmail $email
     * @phpstan-param TNotification $notification
     *
     * @phpstan-return void
     */
    public function send(Email $email, MailNotificationContract $notification): void;
}

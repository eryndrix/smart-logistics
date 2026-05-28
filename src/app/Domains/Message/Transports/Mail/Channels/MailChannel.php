<?php declare(strict_types=1);

namespace App\Domains\Message\Transports\Mail\Channels;

use App\Domains\Message\Transports\Mail\MailNotificationContract;
use App\Domains\Message\Transports\Mail\Providers\MailProviderContract;
use App\Shared\ValueObjects\Email;

/**
 * @phpstan-template TEmail of Email
 * @phpstan-template TNotification of MailNotificationContract<TEmail>
 * @phpstan-implements MailChannelContract<TEmail, TNotification>
 */
final class MailChannel implements MailChannelContract
{
    /**
     * @phpstan-param MailProviderContract<TEmail> $provider
     */
    public function __construct(
        private readonly MailProviderContract $provider
    ) {}

    /**
     * @phpstan-param TEmail $email
     * @phpstan-param TNotification $notification
     *
     * @phpstan-return void
     */
    public function send(Email $email, MailNotificationContract $notification): void
    {
        $mailMessage = $notification->toMail(email: $email);
        $this->provider->send(email: $email, message: $mailMessage);
    }
}

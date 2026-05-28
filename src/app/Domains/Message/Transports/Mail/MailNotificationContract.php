<?php declare(strict_types=1);

namespace App\Domains\Message\Transports\Mail;

use App\Shared\ValueObjects\Email;

/**
 * @phpstan-template TEmail of Email
 */
interface MailNotificationContract
{
    /**
     * @phpstan-param TEmail $email
     * @phpstan-return MailMessage<TEmail>
     */
    public function toMail(Email $email): MailMessage;
}

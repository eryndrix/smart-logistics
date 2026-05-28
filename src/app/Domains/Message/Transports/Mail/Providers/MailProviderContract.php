<?php declare(strict_types=1);

namespace App\Domains\Message\Transports\Mail\Providers;

use App\Shared\ValueObjects\Email;
use App\Domains\Message\Transports\Mail\MailMessage;

/**
 * @template TEmail of Email
 */
interface MailProviderContract
{
    /**
     * @phpstan-param TEmail $email
     * @phpstan-param MailMessage<TEmail> $message
     *
     * @phpstan-return void
     */
    public function send(Email $email, MailMessage $message): void;
}

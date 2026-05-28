<?php declare(strict_types=1);

namespace App\Domains\Message\Transports\Mail\Providers;

use App\Shared\ValueObjects\Email;
use App\Domains\Message\Transports\Mail\MailMessage;
use App\Domains\Message\Transports\Mail\Exceptions\MailInvalidRecipientException;
use App\Domains\Message\Transports\Mail\Exceptions\MailAuthenticationFailedException;
use App\Domains\Message\Transports\Mail\Exceptions\MailInvalidReplyToException;
use App\Domains\Message\Transports\Mail\Exceptions\MailProviderUnavailableException;
use App\Domains\Message\Transports\Mail\Exceptions\MailInvalidSenderException;
use App\Domains\Message\Transports\Mail\Exceptions\MailRateLimitExceededException;
use App\Domains\Message\Transports\Mail\Exceptions\MailTimeoutException;
use Illuminate\Support\Facades\Log;

/**
 * @phpstan-implements MailProviderContract<Email>
 */
final class MailProvider implements MailProviderContract
{
    /**
     * @phpstan-var array<string, list<float>>
     */
    private static array $sent = [];

    /**
     * @phpstan-param Email $email
     * @phpstan-param MailMessage<Email> $message
     *
     * @phpstan-return void
     * 
     * @throws MailRateLimitExceededException
     */
    public function send(Email $email, MailMessage $message): void
    {
        $to = $message->to ?? $email;
        $from = $message->from;
        $replyTo = $message->replyTo;
        $text = $message->body;

        $this->validateRecipient(email: $to);
        $this->validateSender(from: $from, email: $to);
        $this->validateReplyTo(replyTo: $replyTo);
        $this->validateSenderIsNotRecipient(from: $from, email: $to);

        $this->throwIfAuthFailed(text: $text);
        $this->throwIfTimeout(text: $text);
        $this->throwIfUnavailable(text: $text);

        if ($this->hitsRateLimit(email: (string) $to)) {
            throw new MailRateLimitExceededException();
        }

        if ($from !== null) {
            $from = (string) $from['address'];
        }

        if ($replyTo !== null) {
            $replyTo = (string) $replyTo['address'];
        }

        Log::debug(message: 'FAKE MAIL SEND', context: [
            'from' => $from,
            'to' => (string) $to,
            'reply_to' => $replyTo,
            'text' => $text,
            'message_id' => (string) $message->messageId,
        ]);
    }

    /**
     * @phpstan-param Email|null $email
     * @phpstan-return void
     * 
     * @throws MailInvalidRecipientException
     */
    private function validateRecipient(?Email $email): void
    {
        if (!$email instanceof Email) {
            throw new MailInvalidRecipientException();
        }
    }

    /**
     * @phpstan-param array{address: Email, name: string|null}|null $from
     * @phpstan-param Email|null $email
     *
     * @phpstan-return void
     * 
     * @throws MailInvalidSenderException
     */
    private function validateSender(?array $from, ?Email $email): void
    {
        if ($from === null) {
            return;
        }

        if (!isset($from['address'])
            || !$from['address'] instanceof Email
        ) {
            throw new MailInvalidSenderException();
        }
    }

    /**
     * @phpstan-param array{address: Email, name: string|null}|null $replyTo
     * @phpstan-return void
     * 
     * @throws MailInvalidReplyToException
     */
    private function validateReplyTo(?array $replyTo): void
    {
        if ($replyTo === null) {
            return;
        }

        if (!isset($replyTo['address'])
            || !$replyTo['address'] instanceof Email
        ) {
            throw new MailInvalidReplyToException();
        }
    }

    /**
     * @phpstan-param array{address: Email, name: string|null}|null $from
     * @phpstan-param Email|null $email
     *
     * @phpstan-return void
     * 
     * @throws MailInvalidSenderException
     */
    private function validateSenderIsNotRecipient(?array $from, ?Email $email): void
    {
        if ($from === null || !$email instanceof Email) {
            return;
        }

        $address = $from['address'];

        if (($address ?? null) instanceof Email
            && $address->equals(other: $email)
        ) {
            throw new MailInvalidSenderException();
        }
    }

    /**
     * @phpstan-param string $text
     * @phpstan-return void
     * 
     * @throws MailAuthenticationFailedException
     */
    private function throwIfAuthFailed(string $text): void
    {
        if (str_contains(haystack: $text, needle: 'SMTP_AUTH_FAIL')
            || str_contains(haystack: $text, needle: '__AUTH_FAIL__')
        ) {
            throw new MailAuthenticationFailedException();
        }
    }

    /**
     * @phpstan-param string $text
     * @phpstan-return void
     * 
     * @throws MailTimeoutException
     */
    private function throwIfTimeout(string $text): void
    {
        if (str_contains(haystack: $text, needle: 'SMTP_TIMEOUT')
            || str_contains(haystack: $text, needle: '__TIMEOUT__')
        ) {
            throw new MailTimeoutException();
        }
    }

    /**
     * @phpstan-param string $text
     * @phpstan-return void
     * 
     * @throws MailProviderUnavailableException
     */
    private function throwIfUnavailable(string $text): void
    {
        if (str_contains(haystack: $text, needle: 'MAIL_DOWN')
            || str_contains(haystack: $text, needle: 'MAIL_UNAVAILABLE')
            || str_contains(haystack: $text, needle: '__UNAVAILABLE__')
        ) {
            throw new MailProviderUnavailableException();
        }
    }

    /**
     * @phpstan-param string $email
     * @phpstan-return bool
     */
    private function hitsRateLimit(string $email): bool
    {
        $now = microtime(as_float: true);

        $timestamps = array_filter(
            array: self::$sent[$email] ?? [],
            callback: fn(float $ts): bool => ($now - $ts) <= 30
        );

        self::$sent[$email] = array_values(array: $timestamps);

        if (count(value: self::$sent[$email]) >= 5) {
            return true;
        }

        self::$sent[$email][] = $now;

        return false;
    }
}

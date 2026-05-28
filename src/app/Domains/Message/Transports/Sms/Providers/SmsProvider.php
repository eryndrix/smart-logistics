<?php declare(strict_types=1);

namespace App\Domains\Message\Transports\Sms\Providers;

use App\Shared\ValueObjects\Phone;
use App\Domains\Message\Transports\Sms\SmsMessage;
use App\Domains\Message\Transports\Sms\Exceptions\SmsInvalidRecipientException;
use App\Domains\Message\Transports\Sms\Exceptions\SmsAuthenticationFailedException;
use App\Domains\Message\Transports\Sms\Exceptions\SmsInvalidSenderException;
use App\Domains\Message\Transports\Sms\Exceptions\SmsProviderUnavailableException;
use App\Domains\Message\Transports\Sms\Exceptions\SmsRateLimitExceededException;
use App\Domains\Message\Transports\Sms\Exceptions\SmsTimeoutException;
use Illuminate\Support\Facades\Log;

/**
 * @phpstan-implements SmsProviderContract<Phone>
 */
final class SmsProvider implements SmsProviderContract
{
    /**
     * @phpstan-var array<string, list<float>>
     */
    private static array $sent = [];

    /**
     * @phpstan-param Phone $phone
     * @phpstan-param SmsMessage<Phone> $message
     *
     * @phpstan-return void
     * 
     * @throws SmsRateLimitExceededException
     */
    public function send(Phone $phone, SmsMessage $message): void
    {
        $to = $message->to ?? $phone;
        $from = $message->from;
        $text = $message->body;

        $this->validateRecipient(phone: $to);
        $this->validateSender(from: $from);
        $this->validateSenderIsNotRecipient(from: $from, phone: $to);

        $this->throwIfAuthFailed(text: $text);
        $this->throwIfTimeout(text: $text);
        $this->throwIfUnavailable(text: $text);

        if ($this->hitsRateLimit(phone: (string) $to)) {
            throw new SmsRateLimitExceededException();
        }

        Log::info(message: 'FAKE SMS SEND', context: [
            'from' => $from,
            'to' => (string) $to,
            'text' => $text,
            'message_id' => (string) $message->messageId,
        ]);
    }

    /**
     * @phpstan-param Phone|null $phone
     * @phpstan-return void
     * 
     * @throws SmsInvalidRecipientException
     */
    private function validateRecipient(?Phone $phone): void
    {
        if (!$phone instanceof Phone) {
            throw new SmsInvalidRecipientException();
        }

        if (!$this->looksLikePhone(phone: (string) $phone)) {
            throw new SmsInvalidRecipientException();
        }
    }

    /**
     * @phpstan-param string|null $from
     * @phpstan-return void
     * 
     * @throws SmsInvalidSenderException
     */
    private function validateSender(?string $from): void
    {
        if ($from === null) {
            return;
        }

        if (!$this->looksLikePhone($from)) {
            throw new SmsInvalidSenderException();
        }
    }

    /**
     * @phpstan-param string|null $from
     * @phpstan-param Phone|null $phone
     *
     * @phpstan-return void
     * 
     * @throws SmsInvalidSenderException
     */
    private function validateSenderIsNotRecipient(?string $from, ?Phone $phone): void
    {
        if ($from === null || !$phone instanceof Phone) {
            return;
        }

        if ($this->normalizePhone(phone: $from)
            === $this->normalizePhone(phone: (string) $phone)
        ) {
            throw new SmsInvalidSenderException();
        }
    }

    /**
     * @phpstan-param string $text
     * @phpstan-return void
     * 
     * @throws SmsAuthenticationFailedException
     */
    private function throwIfAuthFailed(string $text): void
    {
        if (str_contains(haystack: $text, needle: 'SMS_AUTH_FAIL')
            || str_contains(haystack: $text, needle: '__AUTH_FAIL__')
        ) {
            throw new SmsAuthenticationFailedException();
        }
    }

    /**
     * @phpstan-param string $text
     * @phpstan-return void
     * 
     * @throws SmsTimeoutException
     */
    private function throwIfTimeout(string $text): void
    {
        if (str_contains(haystack: $text, needle: 'SMS_TIMEOUT')
            || str_contains(haystack: $text, needle: '__TIMEOUT__')
        ) {
            throw new SmsTimeoutException();
        }
    }

    /**
     * @phpstan-param string $text
     * @phpstan-return void
     * 
     * @throws SmsProviderUnavailableException
     */
    private function throwIfUnavailable(string $text): void
    {
        if (str_contains(haystack: $text, needle: 'SMS_DOWN')
            || str_contains(haystack: $text, needle: 'SMS_UNAVAILABLE')
            || str_contains(haystack: $text, needle: '__UNAVAILABLE__')
        ) {
            throw new SmsProviderUnavailableException();
        }
    }

    /**
     * @phpstan-param string $phone
     * @phpstan-return bool
     */
    private function hitsRateLimit(string $phone): bool
    {
        $now = microtime(as_float: true);

        $timestamps = array_filter(
            array: self::$sent[$phone] ?? [],
            callback: fn(float $ts): bool => ($now - $ts) <= 30
        );

        self::$sent[$phone] = array_values(array: $timestamps);

        if (count(value: self::$sent[$phone]) >= 5) {
            return true;
        }

        self::$sent[$phone][] = $now;

        return false;
    }

    /**
     * @phpstan-param string $phone
     * @phpstan-return bool
     */
    private function looksLikePhone(string $phone): bool
    {
        $normalized = $this->normalizePhone(phone: $phone);

        return strlen(string: $normalized)
            >= 10 && strlen(string: $normalized) <= 15;
    }

    /**
     * @phpstan-param string $phone
     * @phpstan-return string
     */
    private function normalizePhone(string $phone): string
    {
        return preg_replace(
            pattern: '/\D+/',
            replacement: '',
            subject: $phone
        ) ?? '';
    }
}

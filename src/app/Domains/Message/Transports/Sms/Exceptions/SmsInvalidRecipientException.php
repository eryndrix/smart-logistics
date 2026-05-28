<?php declare(strict_types=1);

namespace App\Domains\Message\Transports\Sms\Exceptions;

/**
 * @phpstan-template TMessage of string
 * @phpstan-template TCode of int
 * @phpstan-template TPrevious of \Throwable|null
 */
final class SmsInvalidRecipientException extends \RuntimeException
{
    /**
     * @phpstan-param TMessage $message
     * @phpstan-param TCode $code
     * @phpstan-param TPrevious $previous
     */
    public function __construct(
        string $message = 'Invalid phone number.',
        int $code = 21211,
        ?\Throwable $previous = null
    ) {
        parent::__construct(
            message: $message,
            code: $code,
            previous: $previous
        );
    }
}
